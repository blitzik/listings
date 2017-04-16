<?php declare(strict_types=1);

namespace Listings\Components;

use Nette\Application\UI\Multiplier;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Common\Components\BaseControl;
use Listings\Listing;

class ListingsOverviewControl extends BaseControl
{
    /** @var IListingControlFactory */
    private $listingControlFactory;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var Listing[] */
    private $listings;

    /** @var  */
    private $year;


    public function __construct(
        int $year,
        ListingFacade $listingFacade,
        IListingControlFactory $listingControlFactory
    ) {
        $this->year = $year;
        $this->listingFacade = $listingFacade;
        $this->listingControlFactory = $listingControlFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingsOverview.latte');

        $this->listings = $this->listingFacade
                               ->findListings(
                                   (new ListingQuery())
                                   ->byOwnerId($this->user->getId())
                                   ->byYear($this->year)
                                   ->orderByMonth('DESC')
                                   ->orderByCreationTime('DESC')
                                   ->indexedById()
                               )->toArray();

        $result = [];
        /** @var Listing $listing */
        foreach ($this->listings as $listing) {
            $result[$listing->getMonth()][] = $listing;
        }

        $template->listingsByMonth = $result;


        $template->render();
    }


    protected function createComponentListing()
    {
        return new Multiplier(function ($id) {
            $comp = $this->listingControlFactory
                         ->create($this->listings[hex2bin($id)]);

            return $comp;
        });
    }
}


interface IListingsOverviewControlFactory
{
    /**
     * @param int $year
     * @return ListingsOverviewControl
     */
    public function create($year);
}