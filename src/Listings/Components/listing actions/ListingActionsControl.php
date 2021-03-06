<?php declare(strict_types=1);

namespace Listings\Components;

use Common\Components\BaseControl;
use Listings\Listing;

class ListingActionsControl extends BaseControl
{
    /** @var Listing */
    private $listing;


    public function __construct(
        Listing $listing
    ) {
        $this->listing = $listing;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingActions.latte');

        $template->listing = $this->listing;


        $template->render();
    }
}


interface IListingActionsControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingActionsControl
     */
    public function create(Listing $listing): ListingActionsControl;
}