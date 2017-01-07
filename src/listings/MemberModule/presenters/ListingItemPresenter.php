<?php

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingItemEditingControlFactory;
use Listings\Services\ListingItemManipulatorFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Users\Authorization\Privilege;
use Nette\Utils\Validators;
use Listings\IListingItem;
use Listings\Listing;

final class ListingItemPresenter extends SecuredPresenter
{
    /**
     * @var IListingItemEditingControlFactory
     * @inject
     */
    public $listingItemEditingControlFactory;

    /**
     * @var ListingItemManipulatorFactory
     * @inject
     */
    public $listingItemManipulatorFactory;

    /**
     * @var ListingFacade
     * @inject
     */
    public $listingFacade;


    /** @var IListingItem|null */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var $int */
    private $day;


    public function actionDefault($listingId, $day)
    {
        if ($listingId === null) {
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->byId($listingId)
                              );

        if (!Validators::is($day, 'numericint') or $day < 1 or $day > $this->listing->getNumberOfDaysInMonth()) {
            $this->redirect(':Listings:Member:ListingDetail:default', ['id' => $this->listing->getId()]);
        }

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default', []);
        }

        $this->day = $day;

        $this->listingItem = $this->listingItemManipulatorFactory
                                  ->getByListing($this->listing)
                                  ->getListingItemByDay((int)$day, $this->listing->getId());

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


    protected function createComponentListingItemEditing()
    {
        $comp = $this->listingItemEditingControlFactory
                     ->create($this->day, $this->listing);

        if ($this->listingItem !== null) {
            $comp->setListingItem($this->listingItem);
        }


        return $comp;
    }


}