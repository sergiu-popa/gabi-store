<?php

namespace App\Controller;

use App\Entity\Money;
use App\Form\MoneyType;
use App\Repository\MoneyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/money")
 */
class MoneyController extends AbstractController
{
    /**
     * @Route("/", name="money_index", methods={"GET"})
     */
    public function index(MoneyRepository $moneyRepository): Response
    {
        return $this->render('money/index.html.twig', [
            'monies' => $moneyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="money_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $money = new Money();
        $form = $this->createForm(MoneyType::class, $money);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($money);
            $entityManager->flush();

            return $this->redirectToRoute('money_index');
        }

        return $this->render('money/new.html.twig', [
            'money' => $money,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="money_show", methods={"GET"})
     */
    public function show(Money $money): Response
    {
        return $this->render('money/show.html.twig', [
            'money' => $money,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="money_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Money $money): Response
    {
        $form = $this->createForm(MoneyType::class, $money);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('money_index');
        }

        return $this->render('money/edit.html.twig', [
            'money' => $money,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="money_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Money $money): Response
    {
        if ($this->isCsrfTokenValid('delete'.$money->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($money);
            $entityManager->flush();
        }

        return $this->redirectToRoute('money_index');
    }
}
