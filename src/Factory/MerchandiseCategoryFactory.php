<?php

namespace App\Factory;

use App\Entity\MerchandiseCategory;
use App\Repository\MerchandiseCategoryRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static MerchandiseCategory|Proxy findOrCreate(array $attributes)
 * @method static MerchandiseCategory|Proxy random()
 * @method static MerchandiseCategory[]|Proxy[] randomSet(int $number)
 * @method static MerchandiseCategory[]|Proxy[] randomRange(int $min, int $max)
 * @method static MerchandiseCategoryRepository|RepositoryProxy repository()
 * @method MerchandiseCategory|Proxy create($attributes = [])
 * @method MerchandiseCategory[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class MerchandiseCategoryFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->unique()->word,
            'code' => self::faker()->randomNumber(3)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(MerchandiseCategory $merchandiseCategory) {})
        ;
    }

    protected static function getClass(): string
    {
        return MerchandiseCategory::class;
    }
}
