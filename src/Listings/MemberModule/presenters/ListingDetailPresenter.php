<?php declare(strict_types=1);

namespace Listings\MemberModule\Presenters;

use Listings\Services\SimpleLunchListingItemManipulator;
use Listings\Services\RangeLunchListingItemManipulator;
use Listings\Components\IListingActionsControlFactory;
use Listings\Components\IListingTableControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Common\Components\FlashMessages\FlashMessage;
use Listings\Services\IListingItemManipulator;
use Listings\Components\ListingActionsControl;
use Listings\Components\ListingTableControl;
use blitzik\Authorization\Privilege;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Listings\Listing;

final class ListingDetailPresenter extends SecuredPresenter
{
    /** @persistent */
    public $displayRemovalForm;


    /**
     * @var SimpleLunchListingItemManipulator
     * @inject
     */
    public $simpleLunchListingItemManipulator;

    /**
     * @var RangeLunchListingItemManipulator
     * @inject
     */
    public $rangeLunchListingItemManipulator;

    /**
     * @var IListingActionsControlFactory
     * @inject
     */
    public $listingActionsControlFactory;

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


    /** @var IListingItemManipulator */
    private $listingItemManipulator;

    /** @var Listing */
    private $listing;


    public function actionDefault($id): void
    {
        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->withEmployer()
                                  ->byId($id)
                              );

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::VIEW)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        if ($this->listing->getItemsType() === Listing::ITEM_TYPE_LUNCH_SIMPLE) {
            $this->listingItemManipulator = $this->simpleLunchListingItemManipulator;
        } else {
            $this->listingItemManipulator = $this->rangeLunchListingItemManipulator;
        }

        $title = $this->setListingPageTitle($this->listing);
        $this['metaTitle']->setTitle($title);
    }


    public function renderDefault($id): void
    {
        $this->template->listing = $this->listing;
        $this->template->displayRemovalForm = (bool)$this->displayRemovalForm;
    }


    protected function createComponentListingActions(): ListingActionsControl
    {
        $comp = $this->listingActionsControlFactory
                     ->create($this->listing);

        return $comp;
    }


    protected function createComponentListingTable(): ListingTableControl
    {
        $comp = $this->listingTableControlFactory
                     ->create($this->listing, $this->listingItemManipulator);

        $comp->onSuccessfulCopyDown[] = [$this, 'onSuccessfulCopyDown'];
        $comp->onSuccessfulRemoval[] = [$this, 'onSuccessfulItemRemoval'];
        $comp->onMissingListing[] = [$this, 'onMissingListing'];

        return $comp;
    }


    public function onSuccessfulCopyDown(): void
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }


    public function onSuccessfulItemRemoval(): void
    {
        if (!$this->isAjax()) {
            $this->redirect('this');
        }
    }


    public function onMissingListing(): void
    {
        $this->flashMessage('Akci nelze dokončit. Výčetka nebyla nalezena.', FlashMessage::WARNING);
        $this->redirect(':Listings:Member:Dashboard:default');
    }
}