<?php

namespace App\Controller;

use App\Entity\Money;
use App\Form\MoneyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/money")
 */
class MoneyController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/new", name="money_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $money = new Money();
        $form = $this->createForm(MoneyType::class, $money);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($money);
            $this->em->flush();

            return $this->redirectToRoute('money_index');
        }

        return $this->render('money/new.html.twig', [
            'money' => $money,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="money_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Money $money): Response
    {
        $form = $this->createForm(MoneyType::class, $money);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('money_index');
        }

        return $this->render('money/edit.html.twig', [
            'money' => $money,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="money_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Money $money): Response
    {
        if ($this->isCsrfTokenValid('delete'.$money->getId(), $request->request->get('_token'))) {
            $money->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Monetarul a fost șterș cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }
}
