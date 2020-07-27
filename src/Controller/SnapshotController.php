<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\Snapshot;
use App\Manager\DayManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class SnapshotController extends AbstractController
{
    /** @var \App\Repository\SnapshotRepository */
    private $snapshotRepository;

    /** @var \App\Repository\ProviderRepository */
    private $providerRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->snapshotRepository = $em->getRepository(Snapshot::class);
        $this->providerRepository = $em->getRepository(Provider::class);
    }

    /**
     * Returns all the snapshots for a specific date.
     * @Route("/istoric/{date}", name="day_history")
     */
    public function log($date = null, Request $request, DayManager $dayManager)
    {
        $date = (new \DateTime($date ?? 'now'))->setTime(0, 0, 0);
        $day = $dayManager->getDay($date);

        // TODO filtru persoana, filtru actiune, filtru tip
        $provider = $request->query->get('provider');
        $providers = $this->providerRepository->findAll();

        return $this->render('log.html.twig', [
            'currentDate' => $date,
            'day' => $day,
            'provider' => $provider,
            'providers' => $providers,
            'transactions' => $dayManager->getTransactions($date),
            'snapshots' => $this->snapshotRepository->findByDate($date, $provider)
        ]);
    }
}
