<?php

namespace Listings\Presenters;

use Listings\Components\IListingItemFormControlFactory;
use Listings\Queries\Factories\ListingItemQueryFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingItemFacade;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Nette\Application\UI\Form;
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

        $this['pageTitle']->setPageTitle('Detail položky');
    }


    public function renderDefault($listingId, $day)
    {

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