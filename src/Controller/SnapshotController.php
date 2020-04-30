<?php

namespace App\Controller;

use App\Repository\SnapshotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */

class SnapshotController extends AbstractController
{
    /**
     * Returns all the snapshots for a specific date.
     * @Route("/istoric/{date}", name="log")
     */
    public function log($date = null, SnapshotRepository $repository)
    {
        $date = new \DateTime($date ?? 'now');

        return $this->render('log.html.twig', [
            'date' => $date,
            'snapshots' => $repository->findByDate($date)
        ]);
    }
}
