<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
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
        $ret['week_start'] = $dto->format('d M');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('d M');

        return $this->formatMonthsToSpanish($ret['week_start'] . ' - ' . $ret['week_end']);
    }

    /**
     * @param string $dateString
     * @return string
     */
    public function formatMonthsToSpanish($dateString)
    {
        $dateString = str_replace('Jan', 'Enero', $dateString);
        $dateString = str_replace('Feb', 'Febrero', $dateString);
        $dateString = str_replace('Mar', 'Marzo', $dateString);
        $dateString = str_replace('Apr', 'Abril', $dateString);
        $dateString = str_replace('May', 'Mayo', $dateString);
        $dateString = str_replace('Jun', 'Junio', $dateString);
        $dateString = str_replace('Jul', 'Julio', $dateString);
        $dateString = str_replace('Aug', 'Agosto', $dateString);
        $dateString = str_replace('Sep', 'Septiembre', $dateString);
        $dateString = str_replace('Oct', 'Octubre', $dateString);
        $dateString = str_replace('Nov', 'Noviembre', $dateString);
        $dateString = str_replace('Dec', 'Diciembre', $dateString);

        return $dateString;
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
