<?php

namespace Listings\Components;

use App\Components\BaseControl;
use Listings\ListingItem;

class ListingItemControl extends BaseControl
{
    /** @var ListingItem */
    private $listingItem;


    public function __construct(
        ListingItem $listingItem
    ) {

        $this->listingItem = $listingItem;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingItem.latte');

        $template->item = $this->listingItem;


        $template->render();
    }
}


interface IListingItemControlFactory
{
    /**
     * @param ListingItem $listingItem
     * @return ListingItemControl
     */
    public function create(ListingItem $listingItem);
}