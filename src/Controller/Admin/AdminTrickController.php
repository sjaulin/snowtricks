<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
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
     * @Route("/admin", name="admin.trick.index")
     */
    public function index()
    {
        $tricks = $this->repository->findAll();
        return $this->render('admin/trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/admin/trick/edit/{id}", name="admin.trick.edit")
     */
    public function edit(Trick $trick, Request $request)
    {

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //TODO Validation sur le nom, si pas déjà exsistant.
            //TODO Bonne pratique, déplacer dans un TrickListener.
            //BUG si nom avec accent
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

            //TODO Bonne pratique, déplacer dans un ImageListener ?
            $this->_addPictures($form, $trick);

            $this->em->flush();
            // TODO Message "ok"
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/create", name="admin.trick.create")
     */
    public function create(Request $request)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //BUG Validation sur le nom, si pas déjà exsistant.
            //TODO Bonne pratique, déplacer dans un TrickListener.
            //BUG si nom avec accent
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

            //TODO Bonne pratique, déplacer dans un ImageListener ?
            $this->_addPictures($form, $trick);

            $this->em->persist($trick); // Also persist pictures by cascade.
            $this->em->flush();

            // TODO Message "ok"
            return $this->redirectToRoute('admin.trick.index');
        }
        return $this->render('admin/trick/create.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/trick/pictures/list/{id}", name="admin.trick.pictures.list")
     * @return Response
     */
    public function pictures_list($id, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->find($id);

        if (!empty($trick)) {
            return $this->render('admin/trick/media.html.twig', [
                'trick' => $trick,
                'pictures' => $trick->getPictures()
            ]);
        }

        throw $this->createNotFoundException('This trick does not exist');
    }

    /**
     * @Route("admin/trick/picture/delete/{id}", name="admin.trick.picture.delete")
     * @return Response
     */
    public function picture_delete($id, PictureRepository $pictureRepository)
    {
        $picture = $pictureRepository->find($id);

        if (!empty($picture)) {
            $file = $this->getParameter('pictures_directory') . '/' . $picture->getName();
            if (is_file($file)) {
                unlink($file);
            }

            $trick = $picture->getTrick();
            $trick->removePicture($picture);
            $this->em->flush();
            // TODO Message "ok"
            return $this->redirectToRoute('admin.trick.pictures.list', ['id' => $trick->getId()]);
        }

        throw $this->createNotFoundException('This picture does not exist');
    }

    private function _addPictures($form, $trick)
    {
        // TODO move to Listener ?
        // Add pictures uploaded
        $pictures = $form->get('pictures')->getData();

        if (!empty($pictures)) {
            foreach ($pictures as $picture) {
                // Save file.
                $filename = md5(uniqid()) . '.' . $picture->guessExtension(); // Require php.ini : extension=fileinfo
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $filename
                );

                // Create Picture entity
                $pictureEntity = new Picture;
                $pictureEntity->setName($filename);
                $trick->addPicture($pictureEntity);
            }
        }

        return $trick;
    }
}
