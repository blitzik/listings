<?php

namespace Listings\Components;

use Listings\Facades\ListingItemFacade;
use Listings\Queries\ListingItemQuery;
use Nette\Application\UI\Multiplier;
use App\Components\BaseControl;
use Listings\ListingItem;
use Listings\Listing;

class ListingTableControl extends BaseControl
{
    /** @var IListingItemControlFactory */
    private $listingItemControlFactory;

    /** @var ListingItemFacade */
    private $listingItemFacade;


    /** @var ListingItem[] */
    private $listingItems;

    /** @var Listing|null */
    private $listing;


    public function __construct(
        Listing $listing,
        ListingItemFacade $listingItemFacade,
        IListingItemControlFactory $listingItemControlFactory
    ) {
        $this->listing = $listing;
        $this->listingItemFacade = $listingItemFacade;
        $this->listingItemControlFactory = $listingItemControlFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingTable.latte');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        $template->daysInMonth = $daysInMonth;

        $this->listingItems = $this->listingItemFacade
                                   ->findListingItems(
                                       (new ListingItemQuery())
                                       ->byListingId($this->listing->getId())
                                       ->indexedByDay()
                                   )->toArray();

        $template->listingItems = $this->listingItems;

        $template->render();
    }


    protected function createComponentListingItem()
    {
        return new Multiplier(function ($day) {
            $comp = $this->listingItemControlFactory
                         ->create($this->listingItems[$day]);

            return $comp;
        });
    }
}


interface IListingTableControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingTableControl
     */
    public function create(Listing $listing);
}