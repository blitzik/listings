<?php

namespace blitzik\Latte\Filters;

use blitzik\Helpers\TimeAgoInWords;

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