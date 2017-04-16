<?php declare(strict_types=1);

namespace blitzik\Latte\Filters;

class HoursAndMinutesFilter
{
    /**
     * @param \DateInterval $dateInterval
     * @return string
     */
    public function __invoke(\DateInterval $dateInterval)
    {
        $result = '';
        $hours = 0;
        if ($dateInterval->days !== false and $dateInterval->days > 0) {
            $hours = $dateInterval->days * 24;
        }

        if ($dateInterval->h > 0) {
            $result = sprintf('%s %s', $dateInterval->h + $hours, $this->plural($dateInterval->h, 'hodina', 'hodiny', 'hodin'));
        }

        if ($dateInterval->i > 0) {
            $result .= sprintf(' %s %s %s', ($dateInterval->h > 0 ? 'a' : null), $dateInterval->i, $this->plural($dateInterval->i, 'minuta', 'minuty', 'minut'));
        }

        return $result;
    }


    /**
     * Plural: three forms, special cases for 1 and 2, 3, 4.
     * (Slavic family: Slovak, Czech)
     * @param  int
     * @return mixed
     */
    private function plural($n)
    {
        $args = func_get_args();
        return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
    }
}