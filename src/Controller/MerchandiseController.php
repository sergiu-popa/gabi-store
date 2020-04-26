<?php

namespace App\Controller;

use App\Entity\Merchandise;
use App\Form\MerchandiseType;
use App\Repository\MerchandiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/merchandise")
 */
class MerchandiseController extends AbstractController
{
    /**
     * @Route("/", name="merchandise_index", methods={"GET"})
     */
    public function index(MerchandiseRepository $merchandiseRepository): Response
    {
        return $this->render('merchandise/index.html.twig', [
            'merchandises' => $merchandiseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="merchandise_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $merchandise = new Merchandise();
        $form = $this->createForm(MerchandiseType::class, $merchandise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($merchandise);
            $entityManager->flush();

            return $this->redirectToRoute('merchandise_index');
        }

        return $this->render('merchandise/new.html.twig', [
            'merchandise' => $merchandise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_show", methods={"GET"})
     */
    public function show(Merchandise $merchandise): Response
    {
        return $this->render('merchandise/show.html.twig', [
            'merchandise' => $merchandise,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="merchandise_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Merchandise $merchandise): Response
    {
        $form = $this->createForm(MerchandiseType::class, $merchandise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('merchandise_index');
        }

        return $this->render('merchandise/edit.html.twig', [
            'merchandise' => $merchandise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="merchandise_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Merchandise $merchandise): Response
    {
        if ($this->isCsrfTokenValid('delete'.$merchandise->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($merchandise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('merchandise_index');
    }
}
