<?php declare(strict_types=1);

namespace blitzik\Latte\Filters;

class CompleteDateFilter
{
    /**
     * @param \DateTimeInterface $dateTime
     * @return string
     */
    public function __invoke(\DateTimeInterface $dateTime)
    {
        $m = $dateTime->format('n');

        $monthName = null;
        switch ($m) {
            case 1: $monthName = 'Ledna'; break;
            case 2: $monthName = 'Února'; break;
            case 3: $monthName = 'Března'; break;
            case 4: $monthName = 'Dubna'; break;
            case 5: $monthName = 'Května'; break;
            case 6: $monthName = 'Června'; break;
            case 7: $monthName = 'Července'; break;
            case 8: $monthName = 'Srpna'; break;
            case 9: $monthName = 'Září'; break;
            case 10: $monthName = 'Října'; break;
            case 11: $monthName = 'Listopadu'; break;
            case 12: $monthName = 'Prosince'; break;
            default: return null;
        }

        $d = null;
        switch ($dateTime->format('w')) {
            case 0: $d = 'Neděle'; break;
            case 1: $d = 'Pondělí'; break;
            case 2: $d = 'Úterý'; break;
            case 3: $d = 'Středa'; break;
            case 4: $d = 'Čtvrtek'; break;
            case 5: $d = 'Pátek'; break;
            case 6: $d = 'Sobota'; break;
        }

        return sprintf(
            '%s %s. %s %s',
            $d,
            $dateTime->format('j'),
            $monthName,
            $dateTime->format('Y')
        );
    }
}