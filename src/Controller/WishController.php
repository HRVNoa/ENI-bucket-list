<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishForm;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function create(Request $request): Response
    {
        $wish = new Wish();
        $form = $this->createForm(WishForm::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateNow = new \DateTime();
            $wish->setIsPublished(true);
            $wish->setDateCreated($dateNow);
            $wish->setDateUpdated($dateNow);

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
    public function update(int $id, Request $request): Response
    {
        $wish = $this->wishRepository->find($id);
        $form = $this->createForm(WishForm::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $this->em->remove($wish);
        $this->em->flush();
        $this->addFlash('success', 'Idea successfully deleted!');
        return $this->redirectToRoute('app_wish_list');
    }
}
