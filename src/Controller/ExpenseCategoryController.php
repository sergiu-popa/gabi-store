<?php

namespace App\Controller;

use App\Entity\ExpenseCategory;
use App\Form\ExpenseCategoryType;
use App\Repository\ExpenseCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/expense/category")
 * @IsGranted("ROLE_ADMIN")
 */
class ExpenseCategoryController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="expense_category_index", methods={"GET"})
     */
    public function index(ExpenseCategoryRepository $expenseCategoryRepository): Response
    {
        return $this->render('expense_category/index.html.twig', [
            'expense_categories' => $expenseCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="expense_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $expenseCategory = new ExpenseCategory();
        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($expenseCategory);
            $this->em->flush();

            return $this->redirectToRoute('expense_category_index');
        }

        return $this->render('expense_category/new.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="expense_category_show", methods={"GET"})
     */
    public function show(ExpenseCategory $expenseCategory): Response
    {
        return $this->render('expense_category/show.html.twig', [
            'expense_category' => $expenseCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="expense_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ExpenseCategory $expenseCategory): Response
    {
        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('expense_category_index');
        }

        return $this->render('expense_category/edit.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="expense_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ExpenseCategory $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $category->delete();
            $this->em->flush();
        }

        return $this->redirectToRoute('expense_category_index');
    }
}
