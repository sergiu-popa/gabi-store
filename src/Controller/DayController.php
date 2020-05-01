<?php

namespace App\Controller;

use App\Entity\Day;
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
     * @Route("/", name="day")
     */
    public function day(DayRepository $dayRepository, Request $request)
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
}
