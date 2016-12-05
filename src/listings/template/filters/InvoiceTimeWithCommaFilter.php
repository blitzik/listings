<?php

declare(strict_types = 1);

namespace Listings\Template\Filters;

use Nette\SmartObject;

final class InvoiceTimeWithCommaFilter
{
    use SmartObject;


    /**
     * @param \Listings\Services\InvoiceTime $invoiceTime
     * @param $withoutTrailingZero
     * @return string
     */
    public function __invoke(\Listings\Services\InvoiceTime $invoiceTime, $withoutTrailingZero = true): string
    {
        list($hours, $minutes, $secs) = sscanf($invoiceTime->getTime(), '%d:%d:%d');

        $result = str_replace('.', ',', bcadd((string)$hours, bcdiv((string)$minutes, '60', 1), 1));
        if ($withoutTrailingZero and mb_substr($result, -1) === '0') {
            return mb_substr($result, 0, -2);
        }

        return $result;
    }
}