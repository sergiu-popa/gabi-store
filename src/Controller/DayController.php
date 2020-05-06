<?php

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayStartType;
use App\Manager\DayManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AbstractController
{
    /** @var DayManager */
    private $manager;

    public function __construct(DayManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="day")
     */
    public function day(Request $request)
    {
        $date = (new \DateTime($request->request->get('date', 'now')))->setTime(0, 0, 0);
        $today = $this->manager->getCurrentDay();

        if ($today === null && $this->dateIsToday($date)) {
            return $this->redirectToRoute('start');
        }

        

        $transactions = $this->manager->getTransactions($date);

        return $this->render('day.html.twig', [
            'showReview' => $request->query->get('showReview', false),
            'currentDate' => $date,
            'canModify' => $this->manager->userCanModifyDay($date),
            'day' => $this->manager->getDay($date),
            'transactions' => $transactions
        ]);
    }

    /**
     * Shows the form to start today by reviewing last day transactions.
     * @Route("/incepe-ziua", name="start")
     */
    public function start(Request $request)
    {
        $form = $this->createForm(DayStartType::class, new Day($this->getUser()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->start($form->getData());

            $this->addFlash(
                'success',
                'Ziua a fost începută la ' . (new \DateTime())->format('H:i')
            );

            return $this->redirectToRoute('day');
        }

        $lastDay = $this->manager->getLastDay();

        return $this->render('start-day.html.twig', [
            'showReview' => true,
            'day' => $this->manager->getLastDay(),
            'currentDate' => $lastDay->getDate(),
            'canModify' => $this->manager->userCanModifyDay($lastDay->getDate()),
            'form' => $form->createView(),
            'transactions' => $this->manager->getTransactionsForLastDay()
        ]);
    }

    private function dateIsToday(\DateTime $date): bool
    {
        return $date->format('Y-m-d') === (new \DateTime())->format('Y-m-d');
    }
}
