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
     * @param int $listingId
     * @return IListingItem[]
     */
    public function findListingItems(int $listingId): array;


    /**
     * @param int $day
     * @param int $listingId
     * @return IListingItem|null
     */
    public function getListingItemByDay(int $day, int $listingId): ?IListingItem;


    /**
     * @param IListingItem $listingItem
     * @return IListingItem
     */
    public function copyDown(IListingItem $listingItem): IListingItem;


    /**
     * @param int $listingItemId
     * @return void
     */
    public function removeListingItem(int $listingItemId): void;


    /**
     * @param int $listingId
     * @return array
     */
    public function loadLocalities(int $listingId): array;
}