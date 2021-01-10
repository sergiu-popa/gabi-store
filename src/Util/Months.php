<?php

namespace App\Util;

class Months
{
    public static function get()
    {
        return [
            '01' => 'Ianuarie',
            '02' => 'Februarie',
            '03' => 'Martie',
            '04' => 'Aprilie',
            '05' => 'Mai',
            '06' => 'Iunie',
            '07' => 'Iulie',
            '08' => 'August',
            '09' => 'Septembrie',
            '10' => 'Octombrie',
            '11' => 'Noiembrie',
            '12' => 'Decembrie',
        ];
    }

    public static function getByNumber(string $representation)
    {
        return [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ][$representation];
    }
}
