<?php

namespace Listings\Presenters;

use Listings\Components\IListingFormControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Users\Authorization\Privilege;
use Listings\Queries\ListingQuery;
use Listings\Listing;

final class ListingPresenter extends SecuredPresenter
{
    /**
     * @var IListingFormControlFactory
     * @inject
     */
    public $listingFormControlFactory;

    /**
     * @var ListingFacade
     * @inject
     */
    public $listingFacade;


    /** @var Listing */
    private $listing;


    public function actionNew()
    {
        $this['pageTitle']->setPageTitle('Nová výčetka');
    }


    public function renderNew()
    {

    }


    protected function createComponentNewListingForm()
    {
        $comp = $this->listingFormControlFactory->create();

        $comp->onSuccessfulSaving[] = function (Listing $listing) {
            $this->redirect(':Listings:ListingDetail:default', ['id' => $listing->getId()]);
        };

        return $comp;
    }


    /*
     * ---------------------------
     * ----- LISTING EDITING -----
     * ---------------------------
     */


    public function actionEdit($id)
    {
        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->byId($id)
                              );

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Dashboard:default', []);
        }

        $this['pageTitle']->setPageTitle('Úprava výčetky');
    }


    public function renderEdit($id)
    {

    }


    protected function createComponentListingEditForm()
    {
        $comp = $this->listingFormControlFactory->create();

        $comp->setListing($this->listing);

        $comp->onSuccessfulSaving[] = function (Listing $listing) {
            $this->redirect(':Listings:ListingDetail:default', ['id' => $listing->getId()]);
        };

        return $comp;
    }


}