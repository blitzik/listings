<?php declare(strict_types=1);

namespace blitzik\Latte\Filters;

use blitzik\Utils\TimeAgoInWords;

class RelativeTimeFilter
{
    /**
     * @param $time
     * @return string
     */
    public function __invoke($time)
    {
        return TimeAgoInWords::get($time);
    }
}