<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class Utilities
{
    /**
     * @return string
     */
    public function getCurrentWeekValue()
    {
        $date = new \DateTime();

        return $date->format("W");
    }

    /**
     * @return string
     */
    public function getCurrentWeek()
    {
        $dtmin = new \DateTime("last sunday");
        $dtmin->modify('+1 day');
        $dtmax = clone($dtmin);
        $dtmax->modify('+6 days');

        return $dtmin->format('d/m/Y') . ' - ' . $dtmax->format('d/m/Y');
    }

    /**
     * @param integer $week
     * @return integer
     */
    public function getNextWeek($week)
    {
        if ($week > 52) {
            return 1;
        }

        return ($week + 1);
    }

    /**
     * @param integer $week
     * @param integer $year
     * @return integer
     */
    public function getNextYear($week, $year)
    {
        if ($week > 52) {
            return ($year + 1);
        }

        return $year;
    }

    /**
     * @param integer $week
     * @return integer
     */
    public function getPreviousWeek($week)
    {
        if ($week < 2) {
            $week = 53;
        }

        return ($week - 1);
    }

    /**
     * @param integer $week
     * @param integer $year
     * @return integer
     */
    public function getPreviousYear($week, $year)
    {
        if ($week < 2) {
            $year = $year - 1;
        }

        return $year;
    }

    /**
     * @param Session $session
     * @return boolean
     */
    public function securityCheck(Session $session)
    {
        $sessionUser = $session->get('user');

        if (empty($sessionUser)) {
            return false;
        }

        if (!$sessionUser->getAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @param integer $year
     * @return integer
     */
    public function getNumberWeeks($year)
    {
        return idate('W', mktime(0, 0, 0, 12, 28, $year));
    }
}
