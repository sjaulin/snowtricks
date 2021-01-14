<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mime\MimeTypes;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(
        TrickRepository $repository,
        SluggerInterface $slugger
    ) {
        $this->repository = $repository;
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
            //TODO Validation sur le nom, si pas déjà exsistant.
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

            //TODO Bonne pratique, déplacer dans un ImageListener ?
            $this->_addPictures($form, $trick);

            $this->getDoctrine()->getManager()->persist($trick); // Also persist pictures by cascade ?
            $this->getDoctrine()->getManager()->persist->flush();
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
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO Validation sur le nom, si pas déjà exsistant.
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));

            //TODO Bonne pratique, déplacer dans un ImageListener ?
            $this->_addPictures($form, $trick);

            $this->getDoctrine()->getManager()->persist($trick);
            $this->getDoctrine()->getManager()->flush();

            $this->em->flush();
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'pictures' => $trick->getPictures(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/trick/pictures/list/{id}", name="admin.trick.pictures.list")
     */
    /*
    public function pictures_list($id, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->findOneBy([
            'id' => $id
        ]);

        return $this->render('admin/trick/pictures.html.twig', [
            'trick' => $trick,
            'pictures' => $trick->getPictures()
        ]);
    }
*/
    private function _addPictures($form, $trick)
    {
        // TODO move to Listener
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
