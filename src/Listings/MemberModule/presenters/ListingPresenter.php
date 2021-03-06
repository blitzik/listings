<?php

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingRemovalControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\IListingFormControlFactory;
use Common\Components\FlashMessages\FlashMessage;
use Listings\Components\ListingRemovalControl;
use Listings\Components\ListingFormControl;
use blitzik\Authorization\Privilege;
use Listings\Facades\ListingFacade;
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


    public function actionNew(): void
    {
        $this['metaTitle']->setTitle('Nová výčetka');
        $this['pageTitle']->setPageTitle('Nová výčetka');
    }


    public function renderNew(): void
    {
    }


    protected function createComponentNewListingForm(): ListingFormControl
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


    public function actionEdit($id): void
    {
        $this->listing = $this->getListingById($id);

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default', []);
        }

        $this['metaTitle']->setTitle('Úprava výčetky');
        $this->setListingPageTitle($this->listing);
    }


    public function renderEdit($id): void
    {
    }


    protected function createComponentListingEditForm(): ListingFormControl
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


    public function actionRemove($id): void
    {
        $this->listing = $this->getListingById($id);

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::EDIT)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Odstranění výčetky');
        $this['pageTitle']->setPageTitle('Odstranění výčetky');
    }


    public function renderRemove($id): void
    {
    }


    protected function createComponentListingRemoval(): ListingRemovalControl
    {
        $comp = $this->listingRemovalControlFactory->create($this->listing);

        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulListingRemoval'];

        return $comp;
    }


    public function onSuccessfulListingRemoval(): void
    {
        $this->flashMessage('Výčetka byla odstraněna.', FlashMessage::SUCCESS);
        $this->redirect(':Listings:Member:Dashboard:default');
    }


    // -----


    private function getListingById($id): ?Listing
    {
        return $this->listingFacade
                    ->getListing(
                        (new ListingQuery())
                        ->byId((int)$id)
                    );
    }


}