<?php

namespace Listings\Presenters;

use Listings\Components\IListingItemFormControlFactory;
use Listings\Queries\Factories\ListingItemQueryFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingItemFacade;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Users\Authorization\Privilege;
use Nette\Utils\Validators;
use Listings\ListingItem;
use Listings\Listing;

class ListingItemPresenter extends SecuredPresenter
{
    /**
     * @var IListingItemFormControlFactory
     * @inject
     */
    public $listingItemFormControlFactory;

    /**
     * @var ListingItemFacade
     * @inject
     */
    public $listingItemFacade;

    /**
     * @var ListingFacade
     * @inject
     */
    public $listingFacade;


    /** @var ListingItem */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var $int */
    private $day;


    public function actionDefault($listingId, $day)
    {
        if ($listingId === null) {
            $this->redirect(':Listings:Dashboard:default');
        }

        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->byId($listingId)
                              );

        if (!Validators::is($day, 'numericint') or $day < 1 or $day > $this->listing->getNumberOfDaysInMonth()) {
            $this->redirect(':Listings:ListingDetail:default', ['id' => $this->listing->getId()]);
        }

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Dashboard:default', []);
        }

        $this->listingItem = $this->listingItemFacade
                                  ->getListingItem(ListingItemQueryFactory::filterByListingAndDay($listingId, $day));

        $this->day = $day;

        $this['metaTitle']->setTitle('Detail položky');
        $this->setListingPageTitle($this->listing);
    }


    public function renderDefault($listingId, $day)
    {
        $this->template->listing = $this->listing;
        $this->template->day = $this->day;
        $this->template->daysInMonth = $this->listing->getNumberOfDaysInMonth();

        $this->template->previous = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day - 1));
        $this->template->date = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));
        $this->template->next = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day + 1));
    }


    protected function createComponentListingItemForm()
    {
        $comp = $this->listingItemFormControlFactory
                     ->create($this->day, $this->listing);

        if ($this->listingItem !== null) {
            $comp->setListingItem($this->listingItem);
        }

        return $comp;
    }


}