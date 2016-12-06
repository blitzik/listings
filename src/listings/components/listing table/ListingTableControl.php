<?php

namespace Listings\Components;

use Listings\Queries\Factories\ListingItemQueryFactory;
use Nette\Application\BadRequestException;
use Listings\Facades\ListingItemFacade;
use Nette\Application\UI\Multiplier;
use App\Components\BaseControl;
use Listings\ListingItem;
use Listings\Listing;

class ListingTableControl extends BaseControl
{
    public $onSuccessfulCopyDown;
    public $onSuccessfulRemoval;
    public $onMissingListing;


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

        if ($this->listingItems === null) {
            $this->listingItems = $this->listingItemFacade
                                       ->findListingItems(
                                           ListingItemQueryFactory::filterByListing($this->listing->getId())
                                           ->indexedByDay()
                                       )->toArray();
        }

        $template->listingItems = $this->listingItems;
        $template->listing = $this->listing;

        $template->render();
    }


    protected function createComponentListingItem()
    {
        return new Multiplier(function ($day) {
            if ($this->listingItems === null) {
                $this->listingItems[$day] = $this->listingItemFacade
                                                 ->getListingItem(ListingItemQueryFactory::filterByListingAndDay($this->listing->getId(), $day));
                if ($this->listingItems[$day] === null) {
                    throw new BadRequestException; // todo
                }
            }
            $item = null;
            if (isset($this->listingItems[$day])) {
                $item = $this->listingItems[$day];
            }
            $comp = $this->listingItemControlFactory
                         ->create($day, $this->listing, $item);

            $comp->onSuccessfulCopyDown[] = [$this, 'onSuccessfullyCopiedListingItemDown'];
            $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfullyRemovedListingItem'];
            $comp->onMissingListing[] = function () {
                $this->onMissingListing();
            };

            return $comp;
        });
    }


    // -----


    public function onSuccessfullyCopiedListingItemDown(ListingItem $listingItem)
    {
        $this->listingItems[$listingItem->getDay()] = $listingItem;
        $this['listingItem'][$listingItem->getDay()]->redrawControl();

        $this->onSuccessfulCopyDown();
    }


    public function onSuccessfullyRemovedListingItem($day)
    {
        unset($this['listingItem'][$day]);
        $this->listingItems = [];
        $this['listingItem'][$day]->redrawControl();

        $this->onSuccessfulRemoval();
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