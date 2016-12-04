<?php

namespace Listings\Components;

use App\Components\BaseControl;
use Listings\Listing;

class ListingControl extends BaseControl
{
    /** @var Listing */
    private $listing;


    public function __construct(
        Listing $listing
    ) {
        $this->listing = $listing;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listing.latte');

        $template->listing = $this->listing;


        $template->render();
    }
}


interface IListingControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingControl
     */
    public function create(Listing $listing);
}