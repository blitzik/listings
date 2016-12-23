<?php

namespace Listings\Presenters;

use Listings\Components\IListingActionsControlFactory;
use Listings\Components\IListingRemovalControlFactory;
use Listings\Components\IListingTableControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Users\Authorization\Privilege;
use Listings\Listing;

final class ListingDetailPresenter extends SecuredPresenter
{
    /** @persistent */
    public $displayRemovalForm;


    /**
     * @var IListingActionsControlFactory
     * @inject
     */
    public $listingActionsControlFactory;

    /**
     * @var IListingRemovalControlFactory
     * @inject
     */
    public $listingRemovalControlFactory;

    /**
     * @var IListingTableControlFactory
     * @inject
     */
    public $listingTableControlFactory;

    /**
     * @var ListingFacade
     * @inject
     */
    public $listingFacade;


    /** @var Listing */
    private $listing;


    public function actionDefault($id)
    {
        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->byId($id)
                              );

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::VIEW)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Dashboard:default', []);
        }

        $this['metaTitle']->setTitle('Detail výčetky');
        $this->setListingPageTitle($this->listing);
    }


    public function renderDefault($id)
    {
        $this->template->listing = $this->listing;
        $this->template->displayRemovalForm = (bool)$this->displayRemovalForm;
    }


    protected function createComponentListingActions()
    {
        $comp = $this->listingActionsControlFactory
                     ->create($this->listing);

        $comp->onDisplayRemovalClick[] = [$this, 'onDisplayRemovalClick'];

        return $comp;
    }


    public function onDisplayRemovalClick(Listing $listing)
    {
        $this->displayRemovalForm = true;
        $this->redirect('this');
    }


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
        $this->displayRemovalForm = null;
        $this->redirect('this');
    }


    protected function createComponentListingTable()
    {
        $comp = $this->listingTableControlFactory->create($this->listing);

        $comp->onSuccessfulCopyDown[] = [$this, 'onSuccessfulCopyDown'];
        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulItemRemoval'];
        $comp->onMissingListing[] = [$this, 'onMissingListing'];

        return $comp;
    }


    public function onSuccessfulCopyDown()
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }


    public function onSuccessfulItemRemoval()
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }


    public function onMissingListing()
    {
        $this->flashMessage('Akci nelze dokončit. Výčetka nebyla nalezena.', FlashMessage::WARNING);
        $this->redirect(':Listings:Dashboard:default');
    }
}