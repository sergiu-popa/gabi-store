<?php

namespace App\Controller;

use App\Entity\DebtPayment;
use App\Form\DebtPaymentType;
use App\Repository\DebtPaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/debt/payment")
 */
class DebtPaymentController extends AbstractController
{
    /**
     * @Route("/", name="debt_payment_index", methods={"GET"})
     */
    public function index(DebtPaymentRepository $debtPaymentRepository): Response
    {
        return $this->render('debt_payment/index.html.twig', [
            'debt_payments' => $debtPaymentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="debt_payment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $debtPayment = new DebtPayment();
        $form = $this->createForm(DebtPaymentType::class, $debtPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($debtPayment);
            $entityManager->flush();

            return $this->redirectToRoute('debt_payment_index');
        }

        return $this->render('debt_payment/new.html.twig', [
            'debt_payment' => $debtPayment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="debt_payment_show", methods={"GET"})
     */
    public function show(DebtPayment $debtPayment): Response
    {
        return $this->render('debt_payment/show.html.twig', [
            'debt_payment' => $debtPayment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="debt_payment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DebtPayment $debtPayment): Response
    {
        $form = $this->createForm(DebtPaymentType::class, $debtPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('debt_payment_index');
        }

        return $this->render('debt_payment/edit.html.twig', [
            'debt_payment' => $debtPayment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="debt_payment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DebtPayment $debtPayment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$debtPayment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($debtPayment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('debt_payment_index');
    }
}
