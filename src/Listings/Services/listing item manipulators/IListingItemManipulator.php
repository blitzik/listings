<?php declare(strict_types=1);

namespace Listings\Services;

use Listings\IListingItem;

interface IListingItemManipulator
{
    /**
     * @param array $data
     * @param IListingItem|null $listingItem
     * @return IListingItem
     */
    public function save(array $data, IListingItem $listingItem = null): IListingItem;


    /**
     * @param string $listingId
     * @return IListingItem[]
     */
    public function findListingItems(string $listingId): array;


    /**
     * @param int $day
     * @param string $listingId
     * @return IListingItem|null
     */
    public function getListingItemByDay(int $day, string $listingId);


    /**
     * @param IListingItem $listingItem
     * @return IListingItem
     */
    public function copyDown(IListingItem $listingItem): IListingItem;


    /**
     * @param string $listingItemId
     * @return void
     */
    public function removeListingItem(string $listingItemId);


    /**
     * @param string $listingId
     * @return array
     */
    public function loadLocalities(string $listingId): array;
}