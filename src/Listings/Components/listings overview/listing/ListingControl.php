<?php declare(strict_types=1);

namespace Listings\Components;

use Common\Components\BaseControl;
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


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listing.latte');

        $template->listing = $this->listing;

        $isCurrentMonth = $this->listing->getDate()->format('Y-m') === date('Y-m');
        $template->isCurrentMonth = $isCurrentMonth;
        $template->currentDay = date('j');

        $template->render();
    }
}


interface IListingControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingControl
     */
    public function create(Listing $listing): ListingControl;
}