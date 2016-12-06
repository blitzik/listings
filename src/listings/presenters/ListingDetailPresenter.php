<?php

namespace Listings\Presenters;

use Listings\Components\IListingTableControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Users\Authorization\Privilege;
use Listings\Listing;

final class ListingDetailPresenter extends SecuredPresenter
{
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

        $this['pageTitle']->setPageTitle('Detail výčetky');
    }


    public function renderDefault($id)
    {
        $this->template->listing = $this->listing;
    }


    protected function createComponentListingTable()
    {
        $comp = $this->listingTableControlFactory->create($this->listing);

        $comp->onSuccessfulCopyDown[] = [$this, 'onSuccessfulCopyDown'];
        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulRemoval'];
        $comp->onMissingListing[] = [$this, 'onMissingListing'];

        return $comp;
    }


    public function onSuccessfulCopyDown()
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }


    public function onSuccessfulRemoval()
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