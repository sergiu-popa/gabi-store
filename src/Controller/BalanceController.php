<?php

namespace App\Controller;

use App\Entity\Balance;
use App\Form\BalanceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/balance")
 */
class BalanceController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/new", name="balance_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $balance = new Balance();
        $form = $this->createForm(BalanceType::class, $balance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($balance);
            $this->em->flush();

            return $this->redirectToRoute('balance_index');
        }

        return $this->render('balance/new.html.twig', [
            'balance' => $balance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="balance_show", methods={"GET"})
     */
    public function show(Balance $balance): Response
    {
        return $this->render('balance/show.html.twig', [
            'balance' => $balance,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="balance_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Balance $balance): Response
    {
        $form = $this->createForm(BalanceType::class, $balance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('balance_index');
        }

        return $this->render('balance/edit.html.twig', [
            'balance' => $balance,
            'form' => $form->createView(),
        ]);
    }
}
