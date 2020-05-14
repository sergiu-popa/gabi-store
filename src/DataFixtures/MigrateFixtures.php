<?php

namespace App\DataFixtures;

use App\Entity\Balance;
use App\Entity\ExpenseCategory;
use App\Entity\ProviderDebt;
use App\Entity\DebtPayment;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Entity\Provider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MigrateFixtures extends Fixture implements FixtureGroupInterface
{
    /** @var Connection */
    private $conn;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->conn = DriverManager::getConnection([
            'url' => 'mysql://root:root@127.0.0.1:3306/magazin_old',
            'charset' => 'UTF8'
        ]);

        $this->encoder = $encoder;
    }

    /**
     * Import Devetech old database to new entities.
     */
    public function load(ObjectManager $manager)
    {
        $this->createUsers($manager);

        $this->migrateFurnizoriToProviders($manager);
        $this->migrateDatoriiToProviderDebt($manager);
        $this->migrateIstoricPlatitToDebtPayment($manager);
        $this->migrateIntrareMarfaToMerchandise($manager);
        $this->migrateMarfaAchitataToMerchandisePayment($manager);

        $this->migrateSabloaneToCategories($manager);
        $this->migrateIesiriDiverseToExpenses($manager);

        $this->migrateMonetarToMoney($manager);
        $this->migrateSoldPrecedentToBalance($manager);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager)
    {
        $plainPassword = 'danexbob';

        $users = ['anda.moiceanu', 'anda.ciobanu', 'gabriel.iarca'];

        foreach ($users as $username) {
            $user = new User();
            $user->setUsername($username);
            $user->setName(ucwords(str_replace('.', ' ', $username)));

            if ($username === 'gabriel.iarca') {
                $plainPassword = '#magazinalimentar123';
                $user->setRoles(['ROLE_ADMIN']);

                $this->addReference('admin', $user);
            }

            $user->setPassword($this->encoder->encodePassword($user, $plainPassword));

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function migrateFurnizoriToProviders(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM furnizori');

        foreach ($stmt->fetchAll() as $row) {
            $provider = new Provider();
            $provider->setName(ucwords(strtolower($row['nume_firma'])));
            $provider->setCui($row['ro_firma']);
            $provider->setTown(ucfirst($row['localitate']));
            $provider->setPhoneNumber($row['tel_sediu']);
            $provider->setAgent(ucfirst($row['agent']));
            $provider->setMobileNumber($row['tel_agent']);

            $manager->persist($provider);

            $this->addReference('Provider'.$row['id'], $provider);
        }
    }

    private function migrateDatoriiToProviderDebt(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM datorii');

        foreach ($stmt->fetchAll() as $row) {
            try {
                $provider = $this->getReference('Provider' . $row['id_firma']);

                $debt = new ProviderDebt();
                $debt->setProvider($provider);
                $debt->setAmount($row['suma']);
                $debt->setDate(new \DateTime($row['data']));
                $debt->setPaidFully($row['platit_integral'] == 1);
                $debt->setPaidPartially($row['platit_partial'] == 1);

                $manager->persist($debt);
                $this->addReference('Debt'.$row['id'], $debt);
            } catch (\OutOfBoundsException $e) {
                print 'Datorii (Debt): ' . $e->getMessage() . "\n";
            }
        }
    }

    private function migrateIstoricPlatitToDebtPayment(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM istoric_plati');

        foreach ($stmt->fetchAll() as $row) {
            try {
                $debt = $this->getReference('Debt' . $row['id_datorie']);

                $payment = new DebtPayment();
                $payment->setDebt($debt);
                $payment->setAmount($row['suma']);
                $payment->setDate(new \DateTime($row['data']));
                $payment->setPaidPartially($row['plata_partial'] == 1);

                $manager->persist($payment);
            } catch (\OutOfBoundsException $e) {
                print 'Istoric plati (DebtPayment): ' . $e->getMessage() . "\n";
            }
        }

        $manager->flush();
    }

    private function migrateIntrareMarfaToMerchandise(ObjectManager $manager)
    {
        $category = (new MerchandiseCategory())->setName('General')->setCode('000');
        $manager->persist($category);

        /*
        $categories = [
            (new MerchandiseCategory())->setName('General'),
            (new MerchandiseCategory())->setName('Alimente'),
            (new MerchandiseCategory())->setName('Racoritoare')
        ];
        foreach($categories as $category) $manager->persist($category);
        */

        $stmt = $this->conn->query('SELECT * FROM intrare_marfa');

        foreach ($stmt->fetchAll() as $row) {
            try {
                $provider = $this->getReference('Provider' . $row['id_firma']);

                $merchandise = new Merchandise();
                $merchandise->setCategory($category);
                //$merchandise->setMerchandiseCategory($categories[array_rand($categories)]);
                $merchandise->setProvider($provider);
                $merchandise->setName($row['denumire']);
                $merchandise->setAmount($row['cantitate']);
                $merchandise->setDate(new \DateTime($row['data']));
                $merchandise->setEnterPrice($row['pret_intrare']);
                $merchandise->setExitPrice($row['pret_iesire']);

                $manager->persist($merchandise);
                $this->addReference('Merchandise-' . $this->merchandiseId($row), $merchandise);
            } catch (\OutOfBoundsException $e) {
                print 'Intrare marfa (MerchandisePayment): ' . $e->getMessage() . "\n";
            }
        }
    }

    private function migrateMarfaAchitataToMerchandisePayment(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM marfa_achitata');

        foreach ($stmt->fetchAll() as $row) {
            try {
                $provider = $this->getReference('Provider' . $row['id_firma']);

                $payment = new MerchandisePayment();
                $payment->setProvider($provider);
                $payment->setAmount($row['suma']);
                $payment->setDate(new \DateTime($row['data']));
                $payment->setType($row['tip_factura']);

                $manager->persist($payment);
            } catch (\OutOfBoundsException $e) {
                print 'Marfa achitate (MerchandisePayment): ' . $e->getMessage() . "\n";
            }
        }

        $manager->flush();
    }

    private function merchandiseId($row)
    {
        return $row['id'] . '-' . $row['data'];
    }

    private function migrateSabloaneToCategories(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM sabloane');

        foreach ($stmt->fetchAll() as $row) {
            $category = new ExpenseCategory();
            $category->setName(empty($row['nume_sablon']) ? 'Retur marfa' : $row['nume_sablon']);

            $manager->persist($category);
            $this->addReference('Category'.$row['id'], $category);

        }
    }

    private function migrateIesiriDiverseToExpenses(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM iesiri_diverse');

        foreach ($stmt->fetchAll() as $row) {
            try {
                $sablonId = $row['id_sablon'];
                $category = $this->getReference('Category' . $sablonId);

                $expense = new Expense();
                $expense->setCategory($category);
                $expense->setAmount($row['suma']);
                $expense->setNotes($row['observatii']);
                $expense->setDate(new \DateTime($row['data']));

                $manager->persist($expense);
            } catch (\OutOfBoundsException $e) {
                print $e->getMessage() . "\n";
            }
        }
    }

    private function migrateMonetarToMoney(ObjectManager $manager)
    {
        $stmt = $this->conn->query('SELECT * FROM monetar');

        foreach ($stmt->fetchAll() as $row) {
            $money = new Money();

            $money->setAmount($row['suma']);
            $money->setNotes($row['observatii']);
            $money->setDate(new \DateTime($row['data']));

            $manager->persist($money);
        }
    }

    private function migrateSoldPrecedentToBalance(ObjectManager $manager)
    {
        // Do not persist duplicated balance for day 2017-11-05 (#335, #336). Persist only the last one.
        $stmt = $this->conn->query('SELECT * FROM sold_precedent WHERE id != 335');

        foreach ($stmt->fetchAll() as $row) {
            $balance = new Balance();

            $balance->setAmount($row['sold_precedent']);
            $balance->setDate(new \DateTime($row['data']));

            $manager->persist($balance);
        }
    }

    public static function getGroups(): array
    {
        return ['migration'];
    }
}
