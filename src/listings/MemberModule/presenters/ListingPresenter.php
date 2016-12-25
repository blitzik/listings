<?php

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingRemovalControlFactory;
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
     * @var IListingRemovalControlFactory
     * @inject
     */
    public $listingRemovalControlFactory;

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
            $this->redirect(':Listings:Member:ListingDetail:default', ['id' => $listing->getId()]);
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
        $this->listing = $this->getListingById($id);

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default', []);
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
            $this->redirect(':Listings:Member:ListingDetail:default', ['id' => $listing->getId()]);
        };

        return $comp;
    }


    /*
     * ---------------------------
     * ----- LISTING REMOVAL -----
     * ---------------------------
     */


    public function actionRemove($id)
    {
        $this->listing = $this->getListingById($id);

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Odstranění výčetky');
        $this['pageTitle']->setPageTitle('Odstranění výčetky');
    }


    public function renderRemove($id)
    {
    }


    protected function createComponentListingRemoval()
    {
        $comp = $this->listingRemovalControlFactory->create($this->listing);

        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulListingRemoval'];

        return $comp;
    }


    public function onSuccessfulListingRemoval()
    {
        $this->flashMessage('Výčetka byla odstraněna.', FlashMessage::SUCCESS);
        $this->redirect(':Listings:Member:Dashboard:default');
    }


    // -----


    /**
     * @param $id
     * @return Listing|null
     */
    private function getListingById($id)
    {
        return $this->listingFacade
                    ->getListing(
                        (new ListingQuery())
                        ->byId($id)
                    );
    }


}