<?php

declare(strict_types = 1);

namespace Listings\Template\Filters;

use Nette\SmartObject;

class InvoiceTimeWithCommaFilter
{
    use SmartObject;


    /**
     * @param \Listings\Services\InvoiceTime $invoiceTime
     * @return string
     */
    public function __invoke(\Listings\Services\InvoiceTime $invoiceTime): string
    {
        list($hours, $minutes, $secs) = sscanf($invoiceTime->getTime(), '%d:%d:%d');

        return str_replace('.', ',', bcadd((string)$hours, bcdiv((string)$minutes, '60', 1), 1));
    }
}