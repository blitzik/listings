<?php

declare(strict_types = 1);

namespace Listings\Template\Filters;

use \Listings\Services\InvoiceTime;
use Nette\SmartObject;

final class InvoiceTimeFilter
{
    use SmartObject;


    /**
     * @param \Listings\Services\InvoiceTime $invoiceTime
     * @param bool $trimLeftZero
     * @return string
     */
    public function __invoke(InvoiceTime $invoiceTime, bool $trimLeftZero = false): string
    {
        return self::convert($invoiceTime, $trimLeftZero);
    }


    /**
     * @param InvoiceTime $invoiceTime
     * @param bool $trimLeftZero
     * @return string
     */
    public static function convert(InvoiceTime $invoiceTime, bool $trimLeftZero = false): string
    {
        $t = $invoiceTime->getTime($trimLeftZero);
        $pos = strrpos($t, ':');

        return mb_substr($t, 0, $pos);
    }
}