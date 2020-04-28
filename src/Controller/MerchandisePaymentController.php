<?php

namespace App\Controller;

use App\Entity\MerchandisePayment;
use App\Form\MerchandisePaymentType;
use App\Repository\MerchandisePaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/merchandise/payment")
 */
class MerchandisePaymentController extends AbstractController
{
    /**
     * @Route("/", name="merchandise_payment_index", methods={"GET"})
     */
    public function index(MerchandisePaymentRepository $merchandisePaymentRepository): Response
    {
        return $this->render('merchandise_payment/index.html.twig', [
            'merchandise_payments' => $merchandisePaymentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="merchandise_payment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $merchandisePayment = new MerchandisePayment();
        $form = $this->createForm(MerchandisePaymentType::class, $merchandisePayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($merchandisePayment);
            $entityManager->flush();

            return $this->redirectToRoute('merchandise_payment_index');
        }

        return $this->render('merchandise_payment/new.html.twig', [
            'merchandise_payment' => $merchandisePayment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_payment_show", methods={"GET"})
     */
    public function show(MerchandisePayment $merchandisePayment): Response
    {
        return $this->render('merchandise_payment/show.html.twig', [
            'merchandise_payment' => $merchandisePayment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="merchandise_payment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MerchandisePayment $merchandisePayment): Response
    {
        $form = $this->createForm(MerchandisePaymentType::class, $merchandisePayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('merchandise_payment_index');
        }

        return $this->render('merchandise_payment/edit.html.twig', [
            'merchandise_payment' => $merchandisePayment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_payment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MerchandisePayment $merchandisePayment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$merchandisePayment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($merchandisePayment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('merchandise_payment_index');
    }
}
