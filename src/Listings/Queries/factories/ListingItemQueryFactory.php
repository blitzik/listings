<?php declare(strict_types=1);

namespace Listings\Queries\Factories;

use Listings\Queries\ListingItemQuery;
use Nette\SmartObject;

class ListingItemQueryFactory
{
    use SmartObject;


    public static function filterByListing(int $listingId): ListingItemQuery
    {
        return (new ListingItemQuery())->byListingId($listingId);
    }


    public static function filterByListingAndDay(int $listingId, int $day): ListingItemQuery
    {
        return self::filterByListing($listingId)->byDay($day);
    }
}