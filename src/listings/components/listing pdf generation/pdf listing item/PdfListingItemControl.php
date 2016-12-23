<?php

namespace Listings\Components;

use Listings\Exceptions\Logic\InvalidArgumentException;
use App\Components\BaseControl;
use Listings\ListingItem;
use Listings\Listing;

class PdfListingItemControl extends BaseControl
{
    const TYPE_DEFAULT = 'default';


    /** @var string */
    private $originalTemplatePath;

    /** @var ListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var string */
    private $type = self::TYPE_DEFAULT;

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

        if ($listingItem !== null and $listingItem->getListingId() !== $listing->getId()) {
            throw new InvalidArgumentException('Given ListingItem does NOT belong to given Listing entity');
        }

        if ($this->listingItem !== null and $this->listingItem->getDay() !== $day) {
            throw new InvalidArgumentException('ListingItem::$day and given parameter does NOT match');
        }
    }


    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    public function render()
    {
        $template = $this->getTemplate();

        $basePath = sprintf('%s/templates/%s', __DIR__, $this->type);
        $this->originalTemplatePath = $basePath . '/layout.latte';
        $template->originalTemplatePath = $this->originalTemplatePath;

        if ($this->listingItem === null) {
            $template->setFile($basePath . '/types/emptyItem.latte');

        } elseif ($this->listingItem->getWorkedHoursWithLunch()->toSeconds() === '0') {
            $template->setFile($basePath . '/types/onlyLocality.latte');

        } else {
            $template->setFile($basePath . '/types/listingItem.latte');
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
}


interface IPdfListingItemControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @param ListingItem $listingItem
     * @return PdfListingItemControl
     */
    public function create($day, Listing $listing, ListingItem $listingItem);
}