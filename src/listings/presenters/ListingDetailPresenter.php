<?php

namespace Listings\Presenters;

use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Listings\Listing;

final class ListingDetailPresenter extends SecuredPresenter
{
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

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, 'view')) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Dashboard:default', []);
        }
    }


    public function renderDefault($id)
    {

    }
}