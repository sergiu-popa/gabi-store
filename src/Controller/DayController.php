<?php

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayEndType;
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
        $day = $this->manager->getDay($date);
        $showReview = $request->query->get('showReview', false);

        if ($day === null && $this->dateIsToday($date)) {
            return $this->redirectToRoute('start');
        }

        if($showReview) {
            $this->addFlash('warning', 'Ca să închizi ziua, verifică fiecare secțiune.');
        }

        $form = $this->createForm(DayEndType::class, $day ?? new Day($this->getUser()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Day $day */
            $day = $form->getData();
            $this->manager->end($day);

            $this->addFlash(
                'success',
                'Ziua a fost închisă la ' . (new \DateTime())->format('H:i')
            );

            return $this->redirectToRoute('day');
        }

        return $this->render('day.html.twig', [
            'showReview' => $showReview,
            'currentDate' => $date,
            'canModify' => $this->manager->userCanModifyDay($date),
            'day' => $day,
            'form' => $form->createView(),
            'transactions' => $this->manager->getTransactions($date),
            'closeAlert' => $this->dontForgetToCloseTheDayAlert($day)
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

        $lastDay = $this->manager->getLastDay();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->start($form->getData());
            $this->manager->confirm($lastDay, $this->getUser());

            $this->addFlash(
                'success',
                'Ziua a fost începută la ' . (new \DateTime())->format('H:i')
            );

            return $this->redirectToRoute('day');
        }

        return $this->render('start-day.html.twig', [
            'showReview' => true,
            'lastDay' => $lastDay,
            'currentDate' => $lastDay->getDate(),
            'canModify' => $this->manager->userCanModifyDay($lastDay->getDate()),
            'form' => $form->createView(),
            'transactions' => $this->manager->getTransactions($lastDay->getDate())
        ]);
    }

    private function dateIsToday(\DateTime $date): bool
    {
        return $date->format('Y-m-d') === (new \DateTime())->format('Y-m-d');
    }

    private function dontForgetToCloseTheDayAlert(?Day $day)
    {
        if($day === null) {
            return false;
        }

        if($day->isToday() === false) {
            return false;
        }

        if($day->isClosed()) {
            return false;
        }

        if(date('H') < 17) {
            return false;
        }

        if(date('H') === 17 && date('i') < 45) {
            return false;
        }

        return true;
    }
}
