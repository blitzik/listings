<?php declare(strict_types=1);

namespace Listings\Template\Filters;

use Listings\Utils\Time\ListingTime;
use Nette\SmartObject;

final class InvoiceTimeWithCommaFilter
{
    use SmartObject;


    /**
     * @param ListingTime $invoiceTime
     * @param $withoutTrailingZero
     * @return string
     */
    public function __invoke(ListingTime $invoiceTime, bool $withoutTrailingZero = true): string
    {
        return self::convert($invoiceTime, $withoutTrailingZero);
    }


    /**
     * @param ListingTime $invoiceTime
     * @param bool $withoutTrailingZero
     * @return string
     */
    public static function convert(ListingTime $invoiceTime, bool $withoutTrailingZero = true): string
    {
        list($hours, $minutes, $secs) = sscanf($invoiceTime->getTime(), '%d:%d:%d');

        $result = str_replace('.', ',', bcadd((string)$hours, bcdiv((string)$minutes, '60', 1), 1));
        if ($withoutTrailingZero and mb_substr($result, -1) === '0') {
            return mb_substr($result, 0, -2);
        }

        return $result;
    }
}