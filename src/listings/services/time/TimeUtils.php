<?php

declare(strict_types = 1);

namespace Listings\Services;

use Nette\SmartObject;

final class TimeUtils
{
    use SmartObject;


    /** @var array */
    private static $months = array(1 => 'Leden', 2 => 'Únor', 3 => 'Březen',
                                   4 => 'Duben', 5 => 'Květen', 6 => 'Červen',
                                   7 => 'Červenec', 8 => 'Srpen', 9 => 'Září',
                                   10 => 'Říjen', 11 => 'Listopad', 12 => 'Prosinec');

    /** @var array */
    private static $days = array(1 => 'Pondělí', 2 => 'Úterý',
                                 3 => 'Středa', 4 => 'Čtvrtek', 5 => 'Pátek',
                                 6 => 'Sobota', 0 => 'Neděle');


    /**
     * @param bool $reversed
     * @return array
     */
    public static function getMonths(bool $reversed = false): array
    {
        if ($reversed === true) {
            $m = self::$months;
            krsort($m);
            return $m;
        }
        return self::$months;
    }


    /**
     * Generates array of years for Select component of Form.
     * Every year this method increases automatically its output by current year
     *
     * @return array Array of years
     */
    public static function generateYearsForSelection(): array
    {
        $base = 2014;
        $currentYear = (int)strftime('%Y');
        $result = array_combine(
            range($base, $currentYear),
            range($base, $currentYear)
        );
        krsort($result);
        return $result;
    }
}