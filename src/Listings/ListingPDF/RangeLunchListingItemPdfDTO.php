<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Services\Time;
use Listings\IListingItem;

class RangeLunchListingItemPdfDTO extends ListingItemPdfDTO
{
    /** @var Time */
    private $lunchStart;

    /** @var Time */
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
     * @return Time
     */
    public function getLunchStart(): Time
    {
        return $this->lunchStart;
    }


    /**
     * @return Time
     */
    public function getLunchEnd(): Time
    {
        return $this->lunchEnd;
    }


}