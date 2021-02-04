<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FileService;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Service\Image as ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickController extends AbstractController
{

    const TRICK_NUMBER = 4;
    const COMMENT_NUMBER = 5;

    public function __construct(
        TrickRepository $repository,
        EntityManagerInterface $em
    ) {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/trick", name="trick_admin")
     */
    public function index()
    {
        $tricks = $this->repository->findAll();
        return $this->render('trick/admin.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(TrickRepository $repository): Response
    {

        $trickCount = $repository->count([]);
        $pageCount = ceil($trickCount / self::TRICK_NUMBER);
        $firstTricks = $repository->findBy([], null, self::TRICK_NUMBER, 0);

        return $this->render('home.html.twig', [
            'tricks' => $firstTricks,
            'pagecount' => $pageCount,
            'nbperpage' => self::TRICK_NUMBER
        ]);
    }

    /**
     * Return tricks HTML
     *
     * @Route ("/trick/listhtml", name="trick_listhtml")
     * @param TrickRepository $repository
     * @return Response
     */
    public function trickListHtml(Request $request, TrickRepository $repository): Response
    {

        $offset = ($request->get('npage') - 1) * self::TRICK_NUMBER;
        $tricks = $repository->findBy([], null, self::TRICK_NUMBER, $offset);
        return $this->render('trick/_list.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/{slug}", name="trick_list")
     */
    public function list($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException('category-not-found');
        }

        $tricks = $category->getTricks();
        return $this->render('trick/list.html.twig', [
            'slug' => $slug,
            'category' => $category,
            'tricks' => $tricks,
        ]);
    }

    /**
     * @Route("trick/new", name="trick_new")
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, FileService $fileService, ImageService $imageService)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setOwner($this->getUser());

            // Save new pictures
            $pictures = $trick->getPictures();
            foreach ($pictures as $picture) {
                /**
                 * @var Picture $picture
                 */
                if ($picture->getFile()) {
                    $filename = $fileService->save(
                        $picture->getFile(),
                        $this->getParameter('uploads_trick_path')
                    );
                    $imageService->crop($this->getParameter('uploads_trick_path') . '/' . $filename, 1.5);
                    $picture->setName($filename);
                    $picture->setTrick($trick);
                } else {
                    $trick->removePicture($picture); // Avoid Bug if a field of collection is empty.
                }
            }

            $this->em->persist($trick); // Also persist pictures et videos by cascade.
            $this->em->flush();

            $this->addFlash('success', 'Le trick a été créé');
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }
        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display a trick
     *
     * @Route("/{category_slug}/{slug}", name="trick_show")
     * @param string $slug
     * @param Request $request
     * @param TrickRepository $trickRepository
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function show(
        $slug,
        Request $request,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository
    ): Response {

        $trick = $trickRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$trick) {
            throw $this->createNotFoundException('Le trick est iconnu');
        }

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        $commentCount = $commentRepository->count([]);
        $pageCount = ceil($commentCount / self::COMMENT_NUMBER);
        $firstComments = $commentRepository->findBy([], null, self::COMMENT_NUMBER, 0);

        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $this->em->persist($comment);
            $this->em->flush();
            $this->addFlash('success', 'Le commentaire a bien été ajouté');
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'pagecount' => $pageCount,
            'nbperpage' => self::COMMENT_NUMBER,
            'comments' => $firstComments
        ]);
    }

    /**
     * Return Comments list HTML
     *
     * @Route ("/comment/listhtml", name="comment_listhtml", priority=1)
     * @param CommentRepository $repository
     * @return Response
     */
    public function commentListHtml(Request $request, CommentRepository $repository): Response
    {

        $offset = ($request->get('npage') - 1) * self::COMMENT_NUMBER;
        $comments = $repository->findBy([], null, self::COMMENT_NUMBER, $offset);
        return $this->render('comment/_list.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @Route("trick/{id}/edit", name="trick_edit")
     */
    public function edit(
        Trick $trick,
        Request $request,
        FileService $fileService,
        ImageService $imageService,
        Filesystem $filesystem
    ) {

        if (!$this->isGranted('ENTITY_EDIT', $trick)) {
            throw new AccessDeniedHttpException("Vous ne pouvez pas modifier ce trick");
        }

        $originalPictures = new ArrayCollection();
        foreach ($trick->getPictures() as $picture) {
            $originalPictures->add($picture);
        }

        $originalVideos = new ArrayCollection();
        foreach ($trick->getVideos() as $video) {
            $originalVideos->add($video);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formPictures = $form->getData()->getPictures()->toArray();

            if (!empty($formPictures)) {
                foreach ($formPictures as $picture) {
                    /** @var Picture $picture */
                    $file = $picture->getFile();
                    if ($file && $file instanceof UploadedFile) {
                        $fileName = $fileService->save($file, $this->getParameter('uploads_trick_path'));
                        $picture->setName($fileName);
                    }
                }
            }

            // Delete old pictures
            foreach ($originalPictures as $picture) {
                /** @var Picture $picture */
                if ($trick->getPictures()->contains($picture) === false) {
                    $filesystem->remove($this->getParameter('uploads_trick_path') . '/' . $picture->getName());
                }
            }

            $this->em->flush();

            $this->addFlash('success', 'Le trick a été modifié');
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("trick/{id}/delete", name="trick_delete")
     * @return Response
     */
    public function delete(Trick $trick, Request $request, Filesystem $filesystem)
    {

        if (!$this->isGranted('ENTITY_DELETE', $trick)) {
            throw new AccessDeniedHttpException("Vous ne pouvez pas supprimer ce trick");
        }

        if (!empty($trick)) {
            if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
                $this->em->remove($trick);
                $this->em->flush();

                $pictures = $trick->getPictures();
                if ($pictures) {
                    foreach ($pictures as $picture) {
                        // TODO doctrine presave
                        $file = $this->getParameter('uploads_trick_directory') . '/' . $picture->getName();
                        if (is_file($file)) {
                            $filesystem->remove($file);
                        }
                    }
                }
                $this->addFlash('success', 'Le trick a été supprimé');
            }
            return $this->redirectToRoute('home');
        }

        throw $this->createNotFoundException('Trick does not exist');
    }
}
