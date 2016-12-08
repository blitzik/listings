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
        return $words[($number == 1) ? 0 : (($number >= 2 && $number <= 4) ? 1 : 2)];
    }
}