<?php

namespace App\Factory;

use App\Entity\Merchandise;
use App\Repository\MerchandiseRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Merchandise|Proxy findOrCreate(array $attributes)
 * @method static Merchandise|Proxy random()
 * @method static Merchandise[]|Proxy[] randomSet(int $number)
 * @method static Merchandise[]|Proxy[] randomRange(int $min, int $max)
 * @method static MerchandiseRepository|RepositoryProxy repository()
 * @method Merchandise|Proxy create($attributes = [])
 * @method Merchandise[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class MerchandiseFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'provider' => ProviderFactory::new()->create(),
            'category' => MerchandiseCategoryFactory::new()->create(),
            'enterPrice' => self::faker()->numberBetween(1, 5),
            'exitPrice' => self::faker()->numberBetween(6, 10),
            'paymentType' => self::faker()->numberBetween(1, 2),
            'name' => self::faker()->word,
            'date' => (new \DateTime())->format('Y-m-d'),
            'amount' => self::faker()->randomNumber(),
            'isDebt' => self::faker()->boolean(75),
        ];
    }

    public function deleted(): self
    {
        return $this->addState(['deleted_at' => self::faker()->dateTime]);
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(Merchandise $merchandise) {})
        ;
    }

    protected static function getClass(): string
    {
        return Merchandise::class;
    }
}
