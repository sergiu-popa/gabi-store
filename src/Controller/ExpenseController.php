<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expense")
 */
class ExpenseController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/new", name="expense_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $expense = new Expense();
        $expense->setDate(new \DateTime($request->query->get('date')));
        $form = $this->createForm(ExpenseType::class, $expense);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($expense);
            $this->em->flush();

            return $this->returnRow($expense);
        }

        return $this->render('expense/form.html.twig', [
            'currentDate' => $request->query->get('date'),
            'expense' => $expense,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="expense_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Expense $expense): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->returnRow($expense);
        }

        return $this->render('expense/form.html.twig', [
            'expense' => $expense,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="expense_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Expense $expense): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $expense->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Ieșirea a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }

    private function returnRow(Expense $expense): Response
    {
        return $this->render('expense/_expense.html.twig', [
            'canModify' => true,
            'e' => $expense
        ]);
    }
}
