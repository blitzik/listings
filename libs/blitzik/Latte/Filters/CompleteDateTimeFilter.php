<?php declare(strict_types=1);

namespace blitzik\Latte\Filters;

class CompleteDateTimeFilter extends CompleteDateFilter
{
    /**
     * @param \DateTimeInterface $dateTime
     * @return string
     */
    public function __invoke(\DateTimeInterface $dateTime)
    {
        $date = parent::__invoke($dateTime);
        return sprintf('%s %s', $date, $dateTime->format('H:i'));
    }
}