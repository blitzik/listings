<?php

namespace blitzik\Latte\Filters;


class PluralFilter
{
    /**
     * @param $number
     * @param array $words
     * @return mixed
     */
    public function __invoke($number, array $words)
    {
        return $words[($number == 0) ? 0 : (($number == 1) ? 1 : (($number >= 2 && $number <= 4) ? 2 : 3))];
    }
}