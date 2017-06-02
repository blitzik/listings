<?php declare(strict_types=1);

namespace Listings;

use Listings\Utils\Time\ListingTime;

interface IListingItem
{
    const LENGTH_LOCALITY = 70;


    public function getListingItemsType(): int;


    public function getDay(): int;


    public function getMonth(): int;


    public function getYear(): int;


    public function getWorkStart(): ListingTime;


    public function getWorkEnd(): ListingTime;


    public function getLunch(): ListingTime;


    public function getWorkedHours(): ListingTime;


    public function getWorkedHoursWithLunch(): ListingTime;


    public function getLocality(): string;
}