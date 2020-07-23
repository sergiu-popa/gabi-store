<?php

namespace App\Factory;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Provider|Proxy findOrCreate(array $attributes)
 * @method static Provider|Proxy random()
 * @method static Provider[]|Proxy[] randomSet(int $number)
 * @method static Provider[]|Proxy[] randomRange(int $min, int $max)
 * @method static ProviderRepository|RepositoryProxy repository()
 * @method Provider|Proxy create($attributes = [])
 * @method Provider[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class ProviderFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->company
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(Provider $provider) {})
        ;
    }

    protected static function getClass(): string
    {
        return Provider::class;
    }
}
