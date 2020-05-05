<?php

namespace App\Controller;

use App\Entity\Merchandise;
use App\Form\MerchandiseType;
use App\Manager\MerchandiseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/merchandise")
 */
class MerchandiseController extends AbstractController
{
    /** @var MerchandiseManager */
    private $manager;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(MerchandiseManager $manager, EntityManagerInterface $em)
    {
        $this->manager = $manager;
        $this->em = $em;
    }

    // TODO render form with provider already selected, besides global form without any provider
    // TODO support collection of form and process it using Collections
    /**
     * @Route("/new", name="merchandise_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $merchandise = new Merchandise($request->query->get('date'));

        $form = $this->createForm(MerchandiseType::class, $merchandise, [
            'provider' => $request->query->get('provider')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($merchandise);
            $this->em->flush();

            return $this->redirectToRoute('merchandise_index');
        }

        return $this->render('merchandise/new.html.twig', [
            'merchandise' => $merchandise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{date}", name="merchandise_index", methods={"GET"})
     */
    public function index($date = null): Response
    {
        $date = new \DateTime($date ?? 'now');

        return $this->render('merchandise/index.html.twig', [
            'providers' => $this->manager->findForDay($date),
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
            $this->em->flush();

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
            $merchandise->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Intrarea a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }
}
