<?php

namespace App\Tests\Integration\Controller;

use App\Entity\MerchandisePayment;
use App\Factory\MerchandiseFactory;
use App\Factory\ProviderFactory;
use App\Repository\MerchandiseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MerchandiseControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->loginUser(self::$container->get(UserRepository::class)->find(1));
    }

    /**
     * @test
     */
    public function new_merchandise_inherits_payment_type_and_is_debt_from_previous_on_same_day()
    {
        // Arrange: create merchandises for today, one deleted and one correct
        $provider = ProviderFactory::new()->create();

        MerchandiseFactory::new()->deleted()->create([
            'provider' => $provider,
            'paymentType' => MerchandisePayment::TYPE_BILL,
            'isDebt' => false
        ]);

        $merchandise = MerchandiseFactory::new()->create([
            'provider' => $provider,
            'paymentType' => MerchandisePayment::TYPE_INVOICE,
            'isDebt' => true
        ]);

        // Act
        $formUri = sprintf('/merchandise/new?date=%s&provider=%d', $merchandise->getDate()->format('Y-m-d'), $provider->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[name]' => 'New merchandise',
            'merchandise[amount]' => 10,
            'merchandise[enterPrice]' => 1,
            'merchandise[exitPrice]' => 2,
            'merchandise[vat]' => 9,
        ]);

        // Assert
        $newMerchandise = self::$container->get(MerchandiseRepository::class)->find(3);

        static::assertSame($merchandise->getPaymentType(), $newMerchandise->getPaymentType());
        static::assertSame($merchandise->getIsDebt(), $newMerchandise->getIsDebt());

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function update_merchandise_exit_price()
    {
        $merchandise = MerchandiseFactory::new()->create([
            'paymentType' => MerchandisePayment::TYPE_INVOICE,
            'isDebt' => true
        ]);

        // Act
        $formUri = sprintf('/merchandise/%d/edit', $merchandise->getId());
        $this->client->request('GET', $formUri);
        $this->client->submitForm('test-submit', [
            'merchandise[exitPrice]' => 100.0,
        ]);

        // Assert
        $updated = self::$container->get(MerchandiseRepository::class)->find($merchandise->getId());

        static::assertSame($updated->getExitPrice(), 100.0);
        static::assertNotEquals($merchandise->getExitPrice(), $updated->getExitPrice());

        $this->assertResponseIsSuccessful();
    }
}
