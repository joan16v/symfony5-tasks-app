<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return TwigFilter
     */
    public function getFilters()
    {
        return [
            new TwigFilter('week_values', [$this, 'getWeekValues']),
            new TwigFilter('day_order', [$this, 'getDayOrder']),
        ];
    }

    /**
     * @param integer $week
     * @param integer $year
     * @return string
     */
    public function getWeekValues($week, $year)
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('d/m/Y');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('d/m/Y');

        return $ret['week_start'] . ' - ' . $ret['week_end'];
    }

    /**
     * @param string $day
     * @return string
     */
    public function getDayOrder($day)
    {
        if ('LUNES' == $day) {
            return '1';
        }
        if ('MARTES' == $day) {
            return '2';
        }
        if ('MIERCOLES' == $day) {
            return '3';
        }
        if ('JUEVES' == $day) {
            return '4';
        }
        if ('VIERNES' == $day) {
            return '5';
        }
        if ('SABADO' == $day) {
            return '6';
        }
        if ('DOMINGO' == $day) {
            return '7';
        }

        return '0';
    }
}
