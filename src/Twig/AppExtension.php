<?php

namespace App\Twig;

use App\Entity\Merchandise;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /** @var string */
    private $locale = 'ro';

    public function getFilters(): array
    {
        return array(
            new TwigFilter('unique_categories', [$this, 'uniqueCategories']),
            new TwigFilter('total', [$this, 'calculateTotal']),
            new TwigFilter('total_category', [$this, 'calculateTotalCategoryExpenses']),
            new TwigFilter('num_for', [$this, 'number_format']),
            new TwigFilter('roDate', [$this, 'roDate'], ['needs_environment' => true]),
            new TwigFilter('dash', [$this, 'dash']),
            new TwigFilter('snapshot_verb', [$this, 'snapshotVerb'], ['is_safe' => ['html']]),
            new TwigFilter('snapshot_content', [$this, 'snapshotContent'], ['is_safe' => ['html']]),
            new TwigFilter('snapshot_type', [$this, 'snapshotType'])
        );
    }

    /**
     * @param Merchandise[] $merchandises
     */
    public function uniqueCategories($merchandises, $categories = []): array
    {
        foreach($merchandises as $merchandise) {
            $category = $merchandise->getCategory();
            $categories[$category->getId()] = $category->getCode();
        }

        return $categories;
    }

    public function calculateTotal($items, $property = 'amount')
    {
        $total = 0;

        foreach($items as $item) {
            if (is_object($item)) {
                $total += $item->{'get' . ucfirst($property)}();
            } else {
                $total += $item[$property];
            }
        }

        return $this->number_format($total);
    }

    function number_format($number, $decimals = 2, $thousands = ' ')
    {
        return number_format((float) $number, $decimals, '.', $thousands);
    }

    /**
     * Returns the verb for the snapshot type.
     */
    public function snapshotVerb(string $type)
    {
        $verbs = [
            'create' => '<span class="badge badge-success">CREARE</span>',
            'update' => '<span class="badge badge-warning">MODIFICARE</span>',
            'delete' => '<span class="badge badge-danger">ȘTERGERE</span>'
        ];

        return $verbs[$type];
    }

    /**
     * Returns the translated content for a snapshot.
     */
    public function snapshotContent(string $json)
    {
        return str_replace(
            ['"','{', '}', ',', ':'],
            ['', '', '', '<br>', ': '],
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
            'Debt' => 'datorie',
            'DebtPayment' => 'plata datorie',
            'Expense' => 'ieșire',
            'Merchandise' => 'intrare marfă',
            'MerchandisePayment' => 'plată marfă',
            'MerchandiseCategory' => 'categorie intrare marfă',
            'Money' => 'monetar',
            'Provider' => 'furnizor',
            'ProviderDebt' => 'datorie furnizor',
        ];

        return mb_strtoupper($types[$type]);
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

    public function roDate($env, $date, $format)
    {
        $dateFormat = 'medium';
        $timeFormat = 'none';
        $locale = 'ro';
        $timezone = 'Europe/Bucharest';
        $calendar = 'gregorian';
        $date = twig_date_converter($env, $date, $timezone);

        $formatValues = array(
            'none' => \IntlDateFormatter::NONE,
            'short' => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long' => \IntlDateFormatter::LONG,
            'full' => \IntlDateFormatter::FULL,
        );

        $formatter = \IntlDateFormatter::create(
            $locale,
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            PHP_VERSION_ID >= 50500 ? $date->getTimezone() : $date->getTimezone()->getName(),
            'gregorian' === $calendar ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL,
            $format
        );

        return $formatter->format($date->getTimestamp());
    }
}
