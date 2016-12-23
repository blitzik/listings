<?php

namespace Listings\Components;

use App\Components\BaseControl;
use Listings\Listing;

class ListingActionsControl extends BaseControl
{
    public $onDisplayRemovalClick;


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
        $template->setFile(__DIR__ . '/listingActions.latte');

        $template->listing = $this->listing;


        $template->render();
    }


    public function handleDisplayRemovalForm()
    {
        $this->onDisplayRemovalClick($this->listing);
    }
}


interface IListingActionsControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingActionsControl
     */
    public function create(Listing $listing);
}