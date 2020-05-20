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

    // TODO support collection of form and process it using Collections
    /**
     * @Route("/new", name="merchandise_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $merchandise = new Merchandise($request->query->get('date'));
        $merchandise->setDate(new \DateTime($request->query->get('date')));
        $provider = $request->query->get('provider');

        $form = $this->createForm(MerchandiseType::class, $merchandise, [
            'provider' => $provider,
            'category' => $request->query->get('category')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($merchandise);
            $this->manager->createPaymentOrDebt($merchandise);
            $this->em->flush();

            return $this->returnRow($merchandise);
        }

        return $this->render('merchandise/form.html.twig', [
            'currentDate' => $request->query->get('date'),
            'includeProvider' => $request->query->get('includeProvider'),
            'provider' => $provider,
            'merchandise' => $merchandise,
            'form' => $form->createView(),
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

            return $this->returnRow($merchandise);
        }

        return $this->render('merchandise/form.html.twig', [
            'includeProvider' => false,
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

    private function returnRow(Merchandise $merchandise): Response
    {
        return $this->render('merchandise/_merchandise.html.twig', [
            'canModify' => true,
            'm' => $merchandise
        ]);
    }
}
