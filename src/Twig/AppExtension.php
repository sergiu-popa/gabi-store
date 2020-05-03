<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /** @var string */
    private $locale = 'ro';

    public function getFilters(): array
    {
        return array(
            new TwigFilter('num_for', [$this, 'number_format']),
            new TwigFilter('roDate', [$this, 'roDate'], ['needs_environment' => true]),
            new TwigFilter('dash', [$this, 'dash']),
            new TwigFilter('snapshot_verb', [$this, 'snapshotVerb']),
            new TwigFilter('snapshot_content', [$this, 'snapshotContent']),
            new TwigFilter('snapshot_type', [$this, 'snapshotType']),
        );
    }

    function number_format($number)
    {
        return number_format((float) $number, 2, '.', ' ');
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
            ['"','{', '}'],
            ['','',''],
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
