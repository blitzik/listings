<?php

namespace Listings\Components;

use Listings\Exceptions\Logic\InvalidArgumentException;
use App\Components\BaseControl;
use Listings\ListingItem;
use Listings\Listing;

class ListingItemControl extends BaseControl
{
    /** @var string */
    private $originalTemplatePath = __DIR__ . '/layout.latte';

    /** @var ListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var int */
    private $day;


    public function __construct(
        int $day,
        Listing $listing,
        ListingItem $listingItem = null
    ) {
        $this->listingItem = $listingItem;
        $this->listing = $listing;
        $this->day = $day;

        if ($this->listingItem !== null and $this->listingItem->getDay() !== $day) {
            throw new InvalidArgumentException('ListingItem::$day and given parameter does NOT match');
        }
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->originalTemplatePath = $this->originalTemplatePath;

        if ($this->listingItem === null) {
            $template->setFile(__DIR__ . '/templates/emptyItem.latte');
        } else {
            $template->setFile(__DIR__ . '/templates/listingItem.latte');
        }

        $template->item = $this->listingItem;
        $template->day = $this->day;

        $itemDate = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));
        $currentDate = \DateTimeImmutable::createFromFormat('!Y-m-d', date('Y-m-d'));
        $template->isCurrentDay = $itemDate == $currentDate;
        $template->itemDate = $itemDate;
        $template->isWeekEnd = $itemDate->format('N') > 5;


        $template->render();
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