<?php declare(strict_types=1);

namespace blitzik\Latte\Filters;

use Nette\SmartObject;

final class WeekDayNameFilter
{
    use SmartObject;


    /** @var array */
    private $days = [1 => 'Pondělí', 2 => 'Úterý',
                     3 => 'Středa', 4 => 'Čtvrtek', 5 => 'Pátek',
                     6 => 'Sobota', 0 => 'Neděle'];


    public function __invoke(\DateTimeInterface $dateTime)
    {
        return $this->days[$dateTime->format('w')];
    }
}