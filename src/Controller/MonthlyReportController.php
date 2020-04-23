<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/reports/monthly", name="monthly")
     */
    public function index()
    {
        return $this->render('monthly/index.html.twig', [
            'controller_name' => 'MonthlyController',
        ]);
    }
}
