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
        );
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
