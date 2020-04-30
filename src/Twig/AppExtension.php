<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('dash', [$this, 'dash']),
            new TwigFilter('snapshot_verb', [$this, 'snapshotVerb']),
            new TwigFilter('snapshot_content', [$this, 'snapshotContent']),
            new TwigFilter('snapshot_type', [$this, 'snapshotType']),
        );
    }

    /**
     * Returns the verb for the snapshot type.
     */
    public function snapshotVerb(string $type)
    {
        $verbs = [
            'create' => 'creat',
            'update' => 'actualizat',
            'delete' => 'șters'
        ];

        return $verbs[$type];
    }

    /**
     * Returns the translated content for a snapshot.
     */
    public function snapshotContent(string $json)
    {
        return str_replace(
            [
                '"',
                '{', '}',
                'amount',
                'name',
                'provider',
                'date',
                'paidPartially',
                'paidTotally',
                'true',
                'false',
                'enterPrice',
                'exitPrice'
            ],
            [
                '',
                '',
                '',
                'cantitate',
                'nume',
                'funizor',
                'data',
                'platit partial',
                'platit total',
                'da',
                'nu',
                'pret intare',
                'pret iesire'
            ],
            $json
        );
    }

    /**
     * Returns the translated entity type for a snapshot.
     */
    public function snapshotType(string $className)
    {
        $type = substr($className, strrpos($className, '\\') + 1);

        $types = [
            'Balance' => 'sold',
            'Debt' => 'datoria',
            'DebtPayment' => 'plata marfă',
            'Expense' => 'ieșire',
            'Merchandise' => 'intrare marfă',
            'MerchandiseCategory' => 'categorie intrare marfă',
            'Money' => 'monetar',
            'Provider' => 'furnizor',
        ];

        return $types[$type];
    }

    /**
     * Returns "-" for 0 or 0.00 strings
     */
    public function dash(string $value): string
    {
        if ($value === '0.00' || $value === '0') {
            return '–';
        }

        return $value;
    }
}
