<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Utils\Time\ListingTime;
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


    public function render(): void
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

        $totalWorkedDays = 0;
        $totalWorkedHours = new ListingTime();
        $listingsByMonth = [];
        /** @var Listing $listing */
        foreach ($this->listings as $listing) {
            $listingsByMonth[$listing->getMonth()][] = $listing;
            $totalWorkedDays += $listing->getWorkedDays();
            $totalWorkedHours = $totalWorkedHours->sum($listing->getWorkedHours());
        }

        $template->listingsByMonth = $listingsByMonth;
        $template->totalWorkedDays = $totalWorkedDays;
        $template->totalWorkedHours = $totalWorkedHours;

        $template->year = $this->year;


        $template->render();
    }


    protected function createComponentListing(): Multiplier
    {
        return new Multiplier(function ($id) {
            $comp = $this->listingControlFactory
                         ->create($this->listings[$id]);

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
    public function create($year): ListingsOverviewControl;
}