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
        $money->setDate(new \DateTime($request->query->get('date')));
        $form = $this->createForm(MoneyType::class, $money);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($money);
            $this->em->flush();

            return $this->returnRow($money);
        }

        return $this->render('money/form.html.twig', [
            'currentDate' => $request->query->get('date'),
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

            return $this->returnRow($money);
        }

        return $this->render('money/form.html.twig', [
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

    private function returnRow(Money $money): Response
    {
        return $this->render('money/_money.html.twig', [
            'canModify' => true,
            'm' => $money
        ]);
    }
}
