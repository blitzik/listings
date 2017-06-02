<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Common\Components\BaseControl;
use Listings\IListingItem;
use Listings\Listing;

class ListingItemEditingControl extends BaseControl
{
    /** @var ILunchRangeListingItemFormControlFactory */
    private $lunchRangeListingItemFormControlFactory;

    /** @var IListingItemFormControlFactory */
    private $listingItemFormControlFactory;


    /** @var IListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var int */
    private $day;


    public function __construct(
        int $day,
        Listing $listing,
        IListingItemFormControlFactory $listingItemFormControlFactory,
        ILunchRangeListingItemFormControlFactory $lunchRangeListingItemFormControlFactory
    ) {
        $this->day = $day;
        $this->listing = $listing;
        $this->listingItemFormControlFactory = $listingItemFormControlFactory;
        $this->lunchRangeListingItemFormControlFactory = $lunchRangeListingItemFormControlFactory;
    }


    /**
     * @param IListingItem $listingItem
     */
    public function setListingItem(IListingItem $listingItem): void
    {
        if ($listingItem->getListingId() !== $this->listing->getId()) {
            throw new InvalidArgumentException;
        }
        $this->listingItem = $listingItem;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(sprintf('%s/templates/%s.latte', __DIR__, $this->listing->getItemsType()));


        $template->render();
    }


    protected function createComponentListingItemForm(): ListingItemFormControl
    {
        $comp = $this->listingItemFormControlFactory
                     ->create($this->day, $this->listing);

        if ($this->listingItem !== null) {
            $comp->setListingItem($this->listingItem);
        }


        return $comp;
    }


    protected function createComponentLunchRangeListingItemForm(): LunchRangeListingItemFormControl
    {
        $comp = $this->lunchRangeListingItemFormControlFactory
                     ->create($this->day, $this->listing);

        if ($this->listingItem !== null) {
            $comp->setListingItem($this->listingItem);
        }


        return $comp;
    }
}


interface IListingItemEditingControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @return ListingItemEditingControl
     */
    public function create(int $day, Listing $listing): ListingItemEditingControl;
}