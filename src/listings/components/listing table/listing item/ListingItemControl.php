<?php

namespace Listings\Components;

use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Facades\ListingItemFacade;
use App\Components\BaseControl;
use Listings\ListingItem;
use Listings\Listing;

class ListingItemControl extends BaseControl
{
    public $onSuccessfulCopyDown;
    public $onSuccessfulRemoval;
    public $onMissingListing;


    /** @var string */
    private $originalTemplatePath = __DIR__ . '/layout.latte';

    /** @var ListingItemFacade */
    private $listingItemFacade;

    /** @var ListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var int */
    private $day;


    public function __construct(
        int $day,
        Listing $listing,
        ListingItem $listingItem = null,
        ListingItemFacade $listingItemFacade
    ) {
        $this->listingItem = $listingItem;
        $this->listing = $listing;
        $this->day = $day;

        if ($listingItem !== null and $listingItem->getListingId() !== $listing->getId()) {
            throw new InvalidArgumentException('Given ListingItem does NOT belong to given Listing entity');
        }

        if ($this->listingItem !== null and $this->listingItem->getDay() !== $day) {
            throw new InvalidArgumentException('ListingItem::$day and given parameter does NOT match');
        }
        $this->listingItemFacade = $listingItemFacade;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->originalTemplatePath = $this->originalTemplatePath;

        if ($this->listingItem === null) {
            $template->setFile(__DIR__ . '/templates/emptyItem.latte');

        } elseif ($this->listingItem->getWorkedHoursWithLunch()->toSeconds() === '0') {
            $template->setFile(__DIR__ . '/templates/onlyLocality.latte');

        } else {
            $template->setFile(__DIR__ . '/templates/listingItem.latte');
        }

        $template->listing = $this->listing;
        $template->item = $this->listingItem;
        $template->day = $this->day;

        $itemDate = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));
        $currentDate = \DateTimeImmutable::createFromFormat('!Y-m-d', date('Y-m-d'));
        $template->isCurrentDay = $itemDate == $currentDate;
        $template->itemDate = $itemDate;
        $template->isWeekEnd = $itemDate->format('N') > 5;


        $template->render();
    }


    public function handleCopyDown()
    {
        try {
            $newListingItem = $this->listingItemFacade->copyDown($this->listingItem);

            $this->onSuccessfulCopyDown($newListingItem);

        } catch (ListingNotFoundException $e) {
            $this->onMissingListing();
        }
    }


    public function handleRemove()
    {
        $this->listingItemFacade->removeListingItem($this->listingItem->getId());
        $this->onSuccessfulRemoval($this->day);
    }
}


interface IListingItemControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @param ListingItem $listingItem
     * @return ListingItemControl
     */
    public function create($day, Listing $listing, ListingItem $listingItem);
}