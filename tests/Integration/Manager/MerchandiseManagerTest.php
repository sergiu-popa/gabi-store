<?php

namespace App\Tests\Integration\Manager;

use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\ProviderDebt;
use App\Factory\MerchandiseFactory;
use App\Manager\MerchandiseManager;
use App\Repository\MerchandisePaymentRepository;
use App\Repository\ProviderDebtRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MerchandiseManagerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    /** @var Merchandise */
    private $merchandisePaidWithDebt;

    /** @var Merchandise */
    private $merchandisePaidWithPayment;

    /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->merchandisePaidWithDebt = $this->createMerchandise();
        $this->merchandisePaidWithPayment = $this->createMerchandise(false);

        $this->client->loginUser(self::$container->get(UserRepository::class)->find(1));
    }

    /** @test */
    public function new_merchandise_as_debt_creates_provider_debt_with_same_amount()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());

        /** @var ProviderDebt $debt */
        $debt = static::$container->get(ProviderDebtRepository::class)->findOneBy([
            'amount' => $this->merchandisePaidWithDebt->getTotalEnterValue()
        ]);

        static::assertSame($this->merchandisePaidWithDebt->getTotalEnterValue(), $debt->getAmount());
        static::assertFalse($debt->getPaidFully());
        static::assertFalse($debt->getPaidPartially());
    }

    /** @test */
    public function new_merchandise_as_debt_increase_existing_provider_debt_with_same_amount()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());

        /** @var MerchandisePayment $debt */
        $debts = static::$container->get(ProviderDebtRepository::class)->findAll();
        $debt = $debts[0];

        static::assertCount(1, $debts);
        static::assertSame($this->merchandisePaidWithDebt->getTotalEnterValue() * 2, $debt->getAmount());
    }

    /** @test */
    public function increment_merchandise_as_debt_increments_debt_with_difference_amount()
    {
        // Create the initial debt for total amount of 100
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());

        // Update the merchandise and update the debt for new amount of 120
        $formUri = sprintf('/merchandise/%d/edit', $this->merchandisePaidWithDebt->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[enterPrice]' => 12.0
        ]);
        $this->merchandisePaidWithDebt->refresh();

        // Check the debt's amount has incremented with the difference 20
        $debt = static::$container->get(ProviderDebtRepository::class)->findAll()[0];
        static::assertSame(120.0, $debt->getAmount());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function decrement_merchandise_as_debt_decrements_debt_with_difference_amount()
    {
        // Create the initial debt for total amount of 100
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());

        // Update the merchandise and update the debt for new amount of 80
        $formUri = sprintf('/merchandise/%d/edit', $this->merchandisePaidWithDebt->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[enterPrice]' => 8.0
        ]);
        $this->merchandisePaidWithDebt->refresh();

        // Check the debt's amount has decremented with the difference 20
        $debt = static::$container->get(ProviderDebtRepository::class)->findAll()[0];
        static::assertSame(80.0, $debt->getAmount());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function delete_merchandise_as_debt_should_delete_provider_debt_too()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithDebt->object());

        $this->client->request('DELETE', '/merchandise/' . $this->merchandisePaidWithDebt->getId(), [
            '_token' => (static::$container->get('security.csrf.token_manager'))
                ->getToken('delete'.$this->merchandisePaidWithDebt->getId())
        ]);

        static::assertEmpty(static::$container->get(ProviderDebtRepository::class)->findAll());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function new_merchandise_creates_payment_with_same_amount()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());

        /** @var MerchandisePayment $payment */
        $payment = static::$container->get(MerchandisePaymentRepository::class)->find(1);

        static::assertSame($this->merchandisePaidWithPayment->getTotalEnterValue(), $payment->getAmount());
        static::assertSame($this->merchandisePaidWithPayment->getPaymentType(), $payment->getPaymentType());
    }

    /** @test */
    public function new_merchandise_increase_existing_payment_with_same_amount()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());

        /** @var MerchandisePayment $payment */
        $payments = static::$container->get(MerchandisePaymentRepository::class)->findAll();
        $payment = $payments[0];

        static::assertCount(1, $payments);
        static::assertSame($this->merchandisePaidWithPayment->getTotalEnterValue() * 2, $payment->getAmount());
        static::assertSame($this->merchandisePaidWithPayment->getPaymentType(), $payment->getPaymentType());
    }

    /** @test */
    public function increment_merchandise_increments_payment_with_difference_amount()
    {
        // Create the initial payment for total amount of 100
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());

        // Update the merchandise and update the payment for new amount of 120
        $formUri = sprintf('/merchandise/%d/edit', $this->merchandisePaidWithPayment->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[enterPrice]' => 12.0
        ]);
        $this->merchandisePaidWithPayment->refresh();

        // Check the payments's amount has incremented with the difference 20
        $payment = static::$container->get(MerchandisePaymentRepository::class)->findAll()[0];
        static::assertSame(120.0, $payment->getAmount());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function decrement_merchandise_decrements_payment_with_difference_amount()
    {
        // Create the initial payment for total amount of 100
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());

        // Update the merchandise and update the payment for new amount of 80
        $formUri = sprintf('/merchandise/%d/edit', $this->merchandisePaidWithPayment->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[enterPrice]' => 8.0
        ]);
        $this->merchandisePaidWithPayment->refresh();

        // Check the payment's amount has decremented with the difference 20
        $payment = static::$container->get(MerchandisePaymentRepository::class)->findAll()[0];
        static::assertSame(80.0, $payment->getAmount());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function delete_merchandise_should_delete_payment_too()
    {
        static::$container->get(MerchandiseManager::class)->createPaymentOrDebt($this->merchandisePaidWithPayment->object());

        $this->client->request('DELETE', '/merchandise/' . $this->merchandisePaidWithPayment->getId(), [
            '_token' => (static::$container->get('security.csrf.token_manager'))
                ->getToken('delete'.$this->merchandisePaidWithPayment->getId())
        ]);

        static::assertEmpty(static::$container->get(MerchandisePaymentRepository::class)->findAll());
        $this->assertResponseIsSuccessful();
    }

    private function createMerchandise($isDebt = true)
    {
        return MerchandiseFactory::new()->create([
            'paymentType' => MerchandisePayment::TYPE_BILL,
            'isDebt' => $isDebt,
            'enterPrice' => 10,
            'exitPrice' => 20,
            'amount' => 10
        ]);
    }
}
