<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

            $this->_addPictures($form->get('pictures')->getData(), $trick);

            $this->em->persist($trick); // Also persist pictures by cascade.
            $this->em->flush();

            $this->addFlash('success', 'Le trick a été créé');
            return $this->redirectToRoute('admin.trick.index');
        }
        return $this->render('admin/trick/create.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/edit/{id}", name="admin.trick.edit")
     */
    public function edit(Trick $trick, Request $request, PictureRepository $pictureRepository)
    {

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //BUG si nom avec accent
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

            $this->_addPictures($form->get('pictures')->getData(), $trick);

            $delete_pictures = $request->get('delete_pictures');
            if ($delete_pictures) {
                foreach ($delete_pictures as $key => $value) {
                    if ($value === 'on') {

                        $delete_picture = $pictureRepository->find($key);
                        $trick->removePicture($delete_picture);

                        // TODO Event doctrine presave
                        $this->_deletePictureFile($delete_picture);
                    }
                }
            }

            $this->em->flush();

            $this->addFlash('success', 'Le trick a été modifié');
            return $this->redirectToRoute('trick.show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'pictures' => $trick->getPictures()
        ]);
    }

    /**
     * @Route("admin/trick/delete/{id}", name="admin.trick.delete")
     * @return Response
     */
    public function delete(Trick $trick, Request $request)
    {
        if (!empty($trick)) {
            $this->em->remove($trick);
            $this->em->flush();

            $pictures = $trick->getPictures();
            if ($pictures) {
                foreach ($pictures as $picture) {
                    // TODO doctrine presave
                    $this->_deletePictureFile($picture);
                }
            }
            $this->addFlash('success', 'Le trick a été supprimé');
            return $this->redirectToRoute('admin.trick.index');
        }

        throw $this->createNotFoundException('Trick does not exist');
    }

    private function _addPictures($pictures, $trick)
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

    private function _deletePictureFile(Picture $picture)
    {
        if ($picture) {
            // delete file
            $file = $this->getParameter('uploads_trick_directory') . '/' . $picture->getName();
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
