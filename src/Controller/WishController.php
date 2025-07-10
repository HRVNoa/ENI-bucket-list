<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishForm;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wishes')]
final class WishController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly WishRepository $wishRepository,
    )
    {
    }

    #[Route('/')]
    public function list(): Response
    {
        $wishes = $this->wishRepository->findBy(
            ['isPublished' => true],
            ['dateCreated' => 'DESC'],
        );

        return $this->render('wish/wish.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function detail(int $id): Response
    {
        $wish = $this->wishRepository->find($id);

        return $this->render('wish/detail.html.twig',[
            'wish' => $wish,
        ]);
    }

    #[Route('/create')]
    public function create(Request $request, #[Autowire('%kernel.project_dir%/public/uploads/image')] string $directory): Response
    {
        $wish = new Wish();
        $form = $this->createForm(WishForm::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile instanceof UploadedFile) {
                $imageFile->move($directory, $imageFile->getClientOriginalName());
                $wish->setPathImage('uploads/image/'.$imageFile->getClientOriginalName());
            }

            $wish->setIsPublished(true);
            $wish->setDateCreated();
            $wish->setDateUpdated();
            $wish->setAuthor($this->getUser());

            $this->em->persist($wish);
            $this->em->flush();

            $this->addFlash('success', 'Idea successfully added!');
            return $this->redirectToRoute('app_wish_list');
        }

        return $this->render('wish/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}')]
    public function update(int $id, Request $request,  #[Autowire('%kernel.project_dir%/public/uploads/image')] string $directory): Response
    {
        $wish = $this->wishRepository->find($id);
        $form = $this->createForm(WishForm::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ( ($form->get('doDelete')->getData() or $imageFile instanceof UploadedFile) and !is_null($wish->getPathImage())) {
                $fileSystem = new Filesystem();
                $fileSystem->remove($wish->getPathImage());
                $wish->setPathImage(null);
            }

            if ($imageFile instanceof UploadedFile) {
                $nameFile = $imageFile->getClientOriginalName();
                $imageFile->move($directory, $nameFile);
                $wish->setPathImage('uploads/image/'.$nameFile);
            }
            $wish->setIsPublished(true);
            $this->em->persist($wish);
            $this->em->flush();

            $this->addFlash('success', 'Idea successfully updated!');
            return $this->redirectToRoute('app_wish_list');
        }

        return $this->render('wish/form.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/delete/{id}', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function delete(int $id): Response
    {
        $wish = $this->wishRepository->find($id);
        if (!$wish){
            throw $this->createNotFoundException('Wish not found');
        }
        if (!is_null($wish->getPathImage())) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($wish->getPathImage());
            $wish->setPathImage(null);
        }
        $this->em->remove($wish);
        $this->em->flush();
        $this->addFlash('success', 'Idea successfully deleted!');
        return $this->redirectToRoute('app_wish_list');
    }
}
