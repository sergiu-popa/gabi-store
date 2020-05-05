<?php

namespace App\Controller;

use App\Entity\MerchandisePayment;
use App\Form\MerchandisePaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/merchandise/payment")
 */
class MerchandisePaymentController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/new", name="merchandise_payment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // TODO full AJAX
        $merchandisePayment = new MerchandisePayment();
        $form = $this->createForm(MerchandisePaymentType::class, $merchandisePayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($merchandisePayment);
            $this->em->flush();

            return $this->redirectToRoute('merchandise_payment_index');
        }

        return $this->render('merchandise_payment/new.html.twig', [
            'merchandise_payment' => $merchandisePayment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="merchandise_payment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MerchandisePayment $merchandisePayment): Response
    {
        // TODO full AJAX
        $form = $this->createForm(MerchandisePaymentType::class, $merchandisePayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

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
    public function delete(Request $request, MerchandisePayment $payment)
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $payment->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Plata a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }
}
