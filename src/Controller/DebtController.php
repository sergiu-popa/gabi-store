<?php

namespace App\Controller;

use App\Entity\Debt;
use App\Form\DebtType;
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
     * @Route("/new", name="debt_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $debt = new Debt();
        $debt->setDate(new \DateTime($request->query->get('date')));
        $form = $this->createForm(DebtType::class, $debt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($debt);
            $this->em->flush();

            return $this->returnRow($debt);
        }

        return $this->render('debt/form.html.twig', [
            'currentDate' => $request->query->get('date'),
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
            $this->em->flush();

            return $this->returnRow($debt);
        }

        return $this->render('debt/form.html.twig', [
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
            $debt->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Datoria a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }

    private function returnRow(Debt $debt): Response
    {
        return $this->render('debt/_debt.html.twig', [
            'canModify' => true,
            'd' => $debt
        ]);
    }
}
