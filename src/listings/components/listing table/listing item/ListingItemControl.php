<?php

namespace Listings\Components;

use Listings\Exceptions\Runtime\ListingNotFoundException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Services\ListingItemManipulatorFactory;
use Listings\Services\IListingItemManipulator;
use App\Components\BaseControl;
use Listings\IListingItem;
use Listings\Listing;

class ListingItemControl extends BaseControl
{
    public $onSuccessfulCopyDown;
    public $onSuccessfulRemoval;
    public $onMissingListing;


    /** @var string */
    private $originalTemplatePath = __DIR__ . '/layout.latte';

    /** @var IListingItemManipulator */
    private $listingItemManipulator;

    /** @var IListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var int */
    private $day;


    public function __construct(
        int $day,
        Listing $listing,
        IListingItem $listingItem = null,
        ListingItemManipulatorFactory $listingItemManipulatorFactory
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

        $this->listingItemManipulator = $listingItemManipulatorFactory->getByListing($listing);
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->originalTemplatePath = $this->originalTemplatePath;

        $itemTemplateBasePath = sprintf('%s/templates/%s', __DIR__, $this->listing->getItemsType());

        if ($this->listingItem === null) {
            $template->setFile(sprintf('%s/%s', $itemTemplateBasePath, 'emptyItem.latte'));

        } elseif ($this->listingItem->getWorkedHoursWithLunch()->toSeconds() === '0') {
            $template->setFile(sprintf('%s/%s', $itemTemplateBasePath, 'onlyLocality.latte'));

        } else {
            $template->setFile(sprintf('%s/%s', $itemTemplateBasePath, 'listingItem.latte'));
        }

        $template->listing = $this->listing;
        $template->item = $this->listingItem;
        $template->day = $this->day;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        $template->daysInMonth = $daysInMonth;

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
            $newListingItem = $this->listingItemManipulator->copyDown($this->listingItem);

            $this->onSuccessfulCopyDown($newListingItem);

        } catch (ListingNotFoundException $e) {
            $this->onMissingListing();
        }
    }


    public function handleRemove()
    {
        $this->listingItemManipulator->removeListingItem($this->listingItem->getId());
        $this->onSuccessfulRemoval($this->day);
    }
}


interface IListingItemControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @param IListingItem $listingItem
     * @return ListingItemControl
     */
    public function create($day, Listing $listing, IListingItem $listingItem);
}