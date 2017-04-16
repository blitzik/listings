<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Services\InvoiceTime;
use Listings\IListingItem;

class RangeLunchListingItemPdfDTO extends ListingItemPdfDTO
{
    /** @var InvoiceTime */
    private $lunchStart;

    /** @var InvoiceTime */
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
     * @return InvoiceTime
     */
    public function getLunchStart(): InvoiceTime
    {
        return $this->lunchStart;
    }


    /**
     * @return InvoiceTime
     */
    public function getLunchEnd(): InvoiceTime
    {
        return $this->lunchEnd;
    }


}