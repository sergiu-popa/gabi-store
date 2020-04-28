<?php

namespace App\Controller;

use App\Entity\MerchandiseCategory;
use App\Form\MerchandiseCategoryType;
use App\Repository\MerchandiseCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/merchandise/category")
 */
class MerchandiseCategoryController extends AbstractController
{
    /**
     * @Route("/", name="merchandise_category_index", methods={"GET"})
     */
    public function index(MerchandiseCategoryRepository $merchandiseCategoryRepository): Response
    {
        return $this->render('merchandise_category/index.html.twig', [
            'merchandise_categories' => $merchandiseCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="merchandise_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $merchandiseCategory = new MerchandiseCategory();
        $form = $this->createForm(MerchandiseCategoryType::class, $merchandiseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($merchandiseCategory);
            $entityManager->flush();

            return $this->redirectToRoute('merchandise_category_index');
        }

        return $this->render('merchandise_category/new.html.twig', [
            'merchandise_category' => $merchandiseCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_category_show", methods={"GET"})
     */
    public function show(MerchandiseCategory $merchandiseCategory): Response
    {
        return $this->render('merchandise_category/show.html.twig', [
            'merchandise_category' => $merchandiseCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="merchandise_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MerchandiseCategory $merchandiseCategory): Response
    {
        $form = $this->createForm(MerchandiseCategoryType::class, $merchandiseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('merchandise_category_index');
        }

        return $this->render('merchandise_category/edit.html.twig', [
            'merchandise_category' => $merchandiseCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MerchandiseCategory $merchandiseCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$merchandiseCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($merchandiseCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('merchandise_category_index');
    }
}
