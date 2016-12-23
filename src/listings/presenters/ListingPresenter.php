<?php

namespace Listings\Presenters;

use Listings\Components\IListingFormControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Components\IListingRemovalControlFactory;
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
        $this['metaTitle']->setTitle('Nová výčetka');
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

        $this['metaTitle']->setTitle('Úprava výčetky');
        $this->setListingPageTitle($this->listing);
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




    /**
     * @var IListingRemovalControlFactory
     * @inject
     */
    public $listingRemovalControlFactory;


    protected function createComponentListingRemoval()
    {
        $comp = $this->listingRemovalControlFactory
                     ->create($this->listing);

        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulListingRemoval'];
        $comp->onCancelClick[] = [$this, 'onRemovalCancelClick'];

        return $comp;
    }


    public function onSuccessfulListingRemoval()
    {
        $this->flashMessage('Výčetka byla úspěšně odstraněna.', FlashMessage::SUCCESS);
        $this->redirect(':Listings:Dashboard:default');
    }


    public function onRemovalCancelClick()
    {
        //$this->displayRemovalForm = null;
        $this->redirect('this');
    }



    public function actionRemove($id)
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

        $this['metaTitle']->setTitle('Zrušení výčetky');
        $this->setListingPageTitle($this->listing);
    }


    public function renderRemove($id)
    {

    }


}