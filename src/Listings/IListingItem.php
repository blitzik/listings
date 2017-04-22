<?php declare(strict_types=1);

namespace Listings;

use Listings\Utils\Time\ListingTime;

interface IListingItem
{
    /**
     * @return int
     */
    public function getListingItemsType(): int;


    /**
     * @return int
     */
    public function getDay(): int;


    /**
     * @return int
     */
    public function getMonth(): int;


    /**
     * @return int
     */
    public function getYear(): int;


    /**
     * @return ListingTime
     */
    public function getWorkStart(): ListingTime;


    /**
     * @return ListingTime
     */
    public function getWorkEnd(): ListingTime;


    /**
     * @return ListingTime
     */
    public function getLunch(): ListingTime;


    /**
     * @return ListingTime
     */
    public function getWorkedHours(): ListingTime;


    /**
     * @return ListingTime
     */
    public function getWorkedHoursWithLunch(): ListingTime;


    /**
     * @return string
     */
    public function getLocality(): string;
}