<?php declare(strict_types=1);

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingItemEditingControlFactory;
use Listings\Services\ListingItemManipulatorFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\ListingItemEditingControl;
use Common\Components\FlashMessages\FlashMessage;
use blitzik\Authorization\Privilege;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
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


    public function actionDefault($listingId, $day): void
    {
        if ($listingId === null) {
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->withSettings()
                                  ->byPresKey($listingId)
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
        $this->setListingPageTitle($this->listing, true);
    }


    public function renderDefault($listingId, $day): void
    {
        $this->template->listing = $this->listing;
        $this->template->day = $this->day;
        $this->template->daysInMonth = $this->listing->getNumberOfDaysInMonth();

        $this->template->previous = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day - 1));
        $this->template->date = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));
        $this->template->next = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day + 1));
    }


    protected function createComponentListingItemEditing(): ListingItemEditingControl
    {
        $comp = $this->listingItemEditingControlFactory
                     ->create((int)$this->day, $this->listing);

        if ($this->listingItem !== null) {
            $comp->setListingItem($this->listingItem);
        }


        return $comp;
    }


}