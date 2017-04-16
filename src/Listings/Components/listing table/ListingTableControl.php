<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Services\ListingItemManipulatorFactory;
use Listings\Services\IListingItemManipulator;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Multiplier;
use Listings\Facades\ListingFacade;
use Listings\Services\InvoiceTime;
use Common\Components\BaseControl;
use Listings\IListingItem;
use Listings\Listing;

class ListingTableControl extends BaseControl
{
    public $onSuccessfulCopyDown;
    public $onSuccessfulRemoval;
    public $onMissingListing;


    /** @var IListingItemControlFactory */
    private $listingItemControlFactory;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var int */
    private $totalWorkedHoursInSeconds;

    /** @var IListingItemManipulator */
    private $listingItemManipulator;

    /** @var int */
    private $totalWorkedDays;

    /** @var IListingItem[] */
    private $listingItems;

    /** @var Listing|null */
    private $listing;


    public function __construct(
        Listing $listing,
        ListingFacade $listingFacade,
        IListingItemControlFactory $listingItemControlFactory,
        ListingItemManipulatorFactory $listingItemManipulatorFactory
    ) {
        $this->listing = $listing;
        $this->listingFacade = $listingFacade;
        $this->listingItemManipulator = $listingItemManipulatorFactory->getByListing($listing);
        $this->listingItemControlFactory = $listingItemControlFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        switch ($this->listing->getItemsType()) {
            case Listing::ITEM_TYPE_LUNCH_RANGE:
                $template->setFile(sprintf('%s/type templates/%s.latte', __DIR__, $this->listing->getItemsType()));
                break;

            default:
                $template->setFile(sprintf('%s/type templates/%s.latte', __DIR__, $this->listing->getItemsType()));
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        $template->daysInMonth = $daysInMonth;

        if ($this->listingItems === null) {
            $this->listingItems = $this->listingItemManipulator->findListingItems($this->listing->getId());
        }

        $template->listingItems = $this->listingItems;
        $template->listing = $this->listing;

        $this->loadListingInfo();

        $template->totalWorkedDays = $this->totalWorkedDays;
        $template->totalWorkedHoursInSeconds = $this->totalWorkedHoursInSeconds;
        $template->totalWorkedHours = new InvoiceTime($this->totalWorkedHoursInSeconds);

        $template->render();
    }


    protected function createComponentListingItem()
    {
        return new Multiplier(function ($day) {
            if ($this->listingItems === null) {
                $this->listingItems[$day] = $this->listingItemManipulator
                                                 ->getListingItemByDay((int)$day, $this->listing->getId());
                if ($this->listingItems[$day] === null) {
                    throw new BadRequestException;
                }
            }
            $item = null;
            if (isset($this->listingItems[$day])) {
                $item = $this->listingItems[$day];
            }
            $comp = $this->listingItemControlFactory
                         ->create($day, $this->listing, $item);

            $comp->onSuccessfulCopyDown[] = [$this, 'onSuccessfullyCopiedListingItemDown'];
            $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfullyRemovedListingItem'];
            $comp->onMissingListing[] = function () {
                $this->onMissingListing();
            };

            return $comp;
        });
    }


    private function loadListingInfo()
    {
        if ($this->totalWorkedDays === null or $this->totalWorkedHoursInSeconds === null) {
            $listingData = $this->listingItemManipulator->getWorkedDaysAndHours($this->listing->getId());
            $this->totalWorkedDays = $listingData['daysCount'];
            $this->totalWorkedHoursInSeconds = $listingData['hoursInSeconds'];
        }
    }


    // -----


    public function onSuccessfullyCopiedListingItemDown(IListingItem $listingItem)
    {
        $this->listingItems[$listingItem->getDay()] = $listingItem;
        $this['listingItem'][$listingItem->getDay()]->redrawControl();

        $this->loadListingInfo();
        $this->redrawControl('listingInfo');

        $this->onSuccessfulCopyDown();
    }


    public function onSuccessfullyRemovedListingItem($day)
    {
        unset($this['listingItem'][$day]);
        $this->listingItems = [];
        $this['listingItem'][$day]->redrawControl();

        $this->loadListingInfo();
        $this->redrawControl('listingInfo');

        $this->onSuccessfulRemoval();
    }
}


interface IListingTableControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingTableControl
     */
    public function create(Listing $listing);
}