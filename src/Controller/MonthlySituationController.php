<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MonthlySituationController extends AbstractController
{
    /**
     * @Route("/monthly/situation/{year}/{month}", name="monthly_situation")
     */
    public function month($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        // TODO get total for month
        $em = $this->getDoctrine()->getManager();
        $money = $em->getRepository(Money::class)->getMonthlySum($year);
        $expense = $em->getRepository(Expense::class)->getMonthlySum($year);
        $payments = $em->getRepository(MerchandisePayment::class)->getMonthlySum($year);
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year);

        /*
        O pagina (care ramana salvata) cu istoricul acelei luni:

        Cifrele mai de la raporte - total vanzare, monetar, iesiri diverse, total marfa facturi, bonuri, profit brut …
        Va contine media adaosului comercial
        Va contine totalul iesirilor diverse pe categorii: salarii, motorina, consumabile magazin, etc.
        Bonusul acordat catre angajati
        Datorii furnizori.
        Sold inceputul de luna si sold la final de luna - adica cu ce sold s-a inceput si cu ce s-ld s-a terminat.

        Iar in partea de jos sa fie urmatoarea chestie:
        Socoteala finala:
        Total iesiri diverse+ monetar = a. (un numar)
        Total sold final- total sold initial = b ( un numar)
        Si acum, ecuatia finala:
        Total profit brut - a = b (daca ecuatia asta nu da asa, atunci inseamna ca avem o problema) - de videntia care este diferenta intre Tolta profit -a si valoarea lui b.
        Daca nu ai inteles, ne mai auzim pe zoom sa iti explic.
        */

        return $this->render('monthly_situation.html.twig', [

        ]);
    }
}
