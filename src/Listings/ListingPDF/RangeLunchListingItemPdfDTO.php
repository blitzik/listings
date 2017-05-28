<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Utils\Time\ListingTime;
use Listings\IListingItem;

class RangeLunchListingItemPdfDTO extends ListingItemPdfDTO
{
    /** @var ListingTime */
    private $lunchStart;

    /** @var ListingTime */
    private $lunchEnd;


    /**
     * @param IListingItem $listingItem
     */
    public function fillByListingItem(IListingItem $listingItem)
    {
        parent::fillByListingItem($listingItem);

        $this->lunchStart = $listingItem->getLunchStart();
        $this->lunchEnd = $listingItem->getLunchEnd();
    }


    /**
     * @return ListingTime
     */
    public function getLunchStart(): ListingTime
    {
        return $this->lunchStart;
    }


    /**
     * @return ListingTime
     */
    public function getLunchEnd(): ListingTime
    {
        return $this->lunchEnd;
    }


}