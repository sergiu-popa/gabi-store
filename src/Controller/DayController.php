<?php

namespace App\Controller;

use App\Entity\Day;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Form\DayType;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard(DayRepository $dayRepository, Request $request)
    {
        $day = $dayRepository->findByDate();

        $form = $this->createForm(DayType::class, $day ?? new Day($this->getUser()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $day = $form->getData();
            $this->em->persist($day);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Ziua a fost începută la ' . $day->getStartedAt()->format('H:i')
            );
        }

        return $this->render('dashboard.html.twig', [
            'day' => $day,
            'form' => $form->createView()
        ]);
    }

    public function day($date, $review = false)
    {
        $date = (new \DateTime($date))->setTime(0, 0, 0);
        $em = $this->getDoctrine()->getManager();

        // TODO calculate totals with collections, please
        $totals = [];
        $merchandise = $em->getRepository(Merchandise::class)->findByDay($date); // TODO group by provider?
        $payments = $em->getRepository(MerchandisePayment::class)->findByDay($date);
        $expenses = $em->getRepository(Expense::class)->findByDay($date);
        $money = $em->getRepository(Money::class)->findByDay($date);

        return $this->render('day.html.twig', [
            'review' => $review,
            'date' => $date,
            'totals' => $totals,
            'merchandise' => $merchandise,
            'payments' => $payments,
            'expenses' => $expenses,
            'money' => $money
        ]);
    }
}
