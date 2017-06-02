<?php declare(strict_types=1);

namespace Listings\Services;

use Nette\SmartObject;
use Listings\Listing;

class ListingItemManipulatorFactory
{
    use SmartObject;


    /** @var SimpleLunchListingItemManipulator */
    private $simpleLunchListingItemManipulator;

    /** @var RangeLunchListingItemManipulator */
    private $rangeLunchListingItemManipulator;


    public function __construct(
        RangeLunchListingItemManipulator $rangeLunchListingItemManipulator,
        SimpleLunchListingItemManipulator $simpleLunchListingItemManipulator
    ) {
        $this->rangeLunchListingItemManipulator = $rangeLunchListingItemManipulator;
        $this->simpleLunchListingItemManipulator = $simpleLunchListingItemManipulator;
    }


    public function getByListing(Listing $listing): IListingItemManipulator
    {
        if ($listing->getItemsType() === Listing::ITEM_TYPE_LUNCH_SIMPLE) {
            return $this->simpleLunchListingItemManipulator;
        }

        return $this->rangeLunchListingItemManipulator;
    }
}