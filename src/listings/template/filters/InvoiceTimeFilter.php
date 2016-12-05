<?php

declare(strict_types = 1);

namespace Listings\Template\Filters;

use Nette\SmartObject;

final class InvoiceTimeFilter
{
    use SmartObject;


    /**
     * @param \Listings\Services\InvoiceTime $invoiceTime
     * @param bool $trimLeftZero
     * @return string
     */
    public function __invoke(\Listings\Services\InvoiceTime $invoiceTime, bool $trimLeftZero = false): string
    {
        $t = $invoiceTime->getTime($trimLeftZero);
        $pos = strrpos($t, ':');

        return mb_substr($t, 0, $pos);
    }
}