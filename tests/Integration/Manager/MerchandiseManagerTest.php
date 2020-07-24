<?php

namespace App\Tests\Integration\Manager;

use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\ProviderDebt;
use App\Factory\MerchandiseFactory;
use App\Manager\MerchandiseManager;
use App\Repository\MerchandisePaymentRepository;
use App\Repository\ProviderDebtRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MerchandiseManagerTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    /**
     * @test
     */
    public function new_merchandise_as_debt_creates_provider_debt()
    {
        /** @var Merchandise $merchandise */
        $merchandise = MerchandiseFactory::new()->create([
            'paymentType' => MerchandisePayment::TYPE_BILL,
            'isDebt' => true,
            'enterPrice' => 10,
            'amount' => 10
        ])->object();

        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($merchandise);

        /** @var ProviderDebt $debt */
        $debt = static::$container->get(ProviderDebtRepository::class)->findOneBy([
            'amount' => $merchandise->getTotalEnterValue()
        ]);

        static::assertSame($merchandise->getTotalEnterValue(), $debt->getAmount());
        static::assertFalse($debt->getPaidFully());
        static::assertFalse($debt->getPaidPartially());
    }

    /**
     * @test
     */
    public function new_merchandise_creates_merchandise_payment()
    {
        /** @var Merchandise $merchandise */
        $merchandise = MerchandiseFactory::new()->create([
            'paymentType' => MerchandisePayment::TYPE_BILL,
            'isDebt' => false,
            'enterPrice' => 10,
            'amount' => 10
        ])->object();

        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($merchandise);

        /** @var MerchandisePayment $payment */
        $payment = static::$container->get(MerchandisePaymentRepository::class)->find(1);

        static::assertSame($merchandise->getTotalEnterValue(), $payment->getAmount());
        static::assertSame($merchandise->getPaymentType(), $payment->getPaymentType());
    }
}
