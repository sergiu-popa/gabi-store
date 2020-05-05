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
        $payment = new MerchandisePayment();
        $payment->setDate(new \DateTime($request->query->get('date')));
        $form = $this->createForm(MerchandisePaymentType::class, $payment);
        $index = (int) $request->query->get('index');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($payment);
            $this->em->flush();

            return $this->returnRow($payment, $index);
        }

        return $this->render('merchandise_payment/form.html.twig', [
            'currentDate' => $request->query->get('date'),
            'index' => $index,
            'payment' => $payment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="merchandise_payment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MerchandisePayment $payment): Response
    {
        $form = $this->createForm(MerchandisePaymentType::class, $payment);
        $index = (int) $request->query->get('index');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->returnRow($payment, $index);
        }

        return $this->render('merchandise_payment/form.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
            'index' => $index
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_payment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MerchandisePayment $payment)
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $payment->delete();
            #$this->em->flush();

            return $this->json(['success' => true, 'message' => 'Plata a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }

    private function returnRow(MerchandisePayment $payment, int $index): Response
    {
        return $this->render('merchandise_payment/_payment.html.twig', [
            'canModify' => true,
            'p' => $payment,
            'index' => $index
        ]);
    }
}
