<?php

declare(strict_types = 1);

namespace Listings\Queries\Factories;

use Listings\Queries\ListingItemQuery;
use Nette\SmartObject;

class ListingItemQueryFactory
{
    use SmartObject;


    /**
     * @param string $listingId
     * @return ListingItemQuery
     */
    public static function filterByListing(string $listingId): ListingItemQuery
    {
        return (new ListingItemQuery())->byListingId($listingId);
    }


    /**
     * @param string $listingId
     * @param int $day
     * @return ListingItemQuery
     */
    public static function filterByListingAndDay(string $listingId, int $day): ListingItemQuery
    {
        return self::filterByListing($listingId)->byDay($day);
    }
}