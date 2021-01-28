<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use App\Repository\PictureRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TrickController extends AbstractController
{

    private $repository;

    public function __construct(
        TrickRepository $repository,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->slugger = $slugger;
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

        $tricks = $repository->findAll();
        return $this->render('home.html.twig', [
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
        ]);
    }

    /**
     * @Route("trick/new", name="trick_new")
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setOwner($this->getUser());
            $this->addPictures($form->get('pictures')->getData(), $trick);
            $this->addVideos($request->get('videos'), $trick);

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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     */
    public function show($slug, Request $request, TrickRepository $trickRepository): Response
    {

        $trick = $trickRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$trick) {
            throw $this->createNotFoundException('Le trick est iconnu');
        }

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

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
            'comments' => $trick->getComments()
        ]);
    }


    /**
     * @Route("trick/{id}/edit", name="trick_edit")
     */
    public function edit(
        Trick $trick,
        Request $request,
        PictureRepository $pictureRepository,
        VideoRepository $videoRepository
    ) {

        if (!$this->isGranted('ENTITY_EDIT', $trick)) {
            throw new AccessDeniedHttpException("Vous ne pouvez pas modifier ce trick");
        }

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addPictures($form->get('pictures')->getData(), $trick);
            $this->addVideos($request->get('videos'), $trick);

            // Add Videos

            // Delete images
            $delete_pictures = $request->get('delete_pictures');
            if ($delete_pictures) {
                foreach ($delete_pictures as $key => $value) {
                    if ($value === 'on') {
                        $delete_picture = $pictureRepository->find($key);
                        $trick->removePicture($delete_picture);
                        $file = $this->getParameter('uploads_trick_directory') . '/' . $delete_picture->getName();
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                }
            }

            // Delete videos
            $delete_videos = $request->get('delete_videos');
            if ($delete_videos) {
                foreach ($delete_videos as $key => $value) {
                    if ($value === 'on') {
                        $delete_video = $videoRepository->find($key);
                        $trick->removeVideo($delete_video);
                    }
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
    public function delete(Trick $trick, Request $request)
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
                            unlink($file);
                        }
                    }
                }
                $this->addFlash('success', 'Le trick a été supprimé');
            }
            return $this->redirectToRoute('home');
        }

        throw $this->createNotFoundException('Trick does not exist');
    }

    // TODO : Create service AND Doctrine EventListener
    private function addPictures($pictures, $trick)
    {
        if (!empty($pictures)) {
            foreach ($pictures as $picture) {
                // Save file.
                $filename = md5(uniqid()) . '.' . $picture->guessExtension(); // Require php.ini : extension=fileinfo
                $picture->move(
                    $this->getParameter('uploads_trick_path'),
                    $filename
                );

                // Create Picture entity
                $pictureEntity = new Picture;
                $pictureEntity->setName($filename);

                // Attach to trick
                $trick->addPicture($pictureEntity);
            }
        }

        return $trick;
    }

    private function addVideos($videos, $trick)
    {
        $url_videos = $videos;
        if ($url_videos) {
            foreach ($url_videos as $url_video) {
                if (!empty($url_video)) {
                    $video = new Video;
                    $video->setUrl($url_video);
                    //$this->em->persist($video);
                    $trick->addVideo($video);
                }
            }
        }

        return $trick;
    }
}
