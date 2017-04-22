<?php declare(strict_types=1);

namespace Listings\Template\Filters;

use Listings\Utils\Time\ListingTime;
use Nette\SmartObject;

final class InvoiceTimeFilter
{
    use SmartObject;


    /**
     * @param ListingTime $invoiceTime
     * @param bool $trimLeftZero
     * @return string
     */
    public function __invoke(ListingTime $invoiceTime, bool $trimLeftZero = false): string
    {
        return self::convert($invoiceTime, $trimLeftZero);
    }


    /**
     * @param ListingTime $invoiceTime
     * @param bool $trimLeftZero
     * @return string
     */
    public static function convert(ListingTime $invoiceTime, bool $trimLeftZero = false): string
    {
        $time = mb_substr($invoiceTime->getTime(), 0, strrpos($invoiceTime->getTime(), ':'));
        if ($trimLeftZero === true) {
            $time = ltrim($time, '0');
            if ($time[0] === ':') {
                $time = '0' . $time;
            }
        }
        return $time;
    }
}