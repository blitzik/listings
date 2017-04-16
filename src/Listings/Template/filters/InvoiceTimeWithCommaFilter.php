<?php declare(strict_types=1);

namespace Listings\Template\Filters;

use Listings\Services\InvoiceTime;
use Nette\SmartObject;

final class InvoiceTimeWithCommaFilter
{
    use SmartObject;


    /**
     * @param InvoiceTime $invoiceTime
     * @param $withoutTrailingZero
     * @return string
     */
    public function __invoke(InvoiceTime $invoiceTime, bool $withoutTrailingZero = true): string
    {
        return self::convert($invoiceTime, $withoutTrailingZero);
    }


    /**
     * @param InvoiceTime $invoiceTime
     * @param bool $withoutTrailingZero
     * @return string
     */
    public static function convert(InvoiceTime $invoiceTime, bool $withoutTrailingZero = true): string
    {
        list($hours, $minutes, $secs) = sscanf($invoiceTime->getTime(), '%d:%d:%d');

        $result = str_replace('.', ',', bcadd((string)$hours, bcdiv((string)$minutes, '60', 1), 1));
        if ($withoutTrailingZero and mb_substr($result, -1) === '0') {
            return mb_substr($result, 0, -2);
        }

        return $result;
    }
}