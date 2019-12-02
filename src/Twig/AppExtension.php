<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('week_values', [$this, 'getWeekValues']),
        ];
    }

    public function getWeekValues($week, $year)
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('d/m/Y');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('d/m/Y');

        return $ret['week_start'] . ' - ' . $ret['week_end'];
    }
}
