<?php

namespace App\Controller;

use App\Entity\Debt;
use App\Form\DebtType;
use App\Repository\DebtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/debt")
 */
class DebtController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="debt_index", methods={"GET"})
     */
    public function index(DebtRepository $debtRepository): Response
    {
        return $this->render('debt/index.html.twig', [
            'debts' => $debtRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="debt_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $debt = new Debt();
        $form = $this->createForm(DebtType::class, $debt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($debt);
            $this->em->flush();

            return $this->redirectToRoute('debt_index');
        }

        return $this->render('debt/new.html.twig', [
            'debt' => $debt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="debt_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Debt $debt): Response
    {
        $form = $this->createForm(DebtType::class, $debt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('debt_index');
        }

        return $this->render('debt/edit.html.twig', [
            'debt' => $debt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="debt_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Debt $debt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$debt->getId(), $request->request->get('_token'))) {
            $debt->delete($this->getUser());
            $this->em->flush();

            $this->addFlash('success', 'Datoria a fost stearsa cu success.');
        }

        return $this->redirectToRoute('debt_index');
    }
}
