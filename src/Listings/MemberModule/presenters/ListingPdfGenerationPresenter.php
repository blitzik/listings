<?php

namespace Listings\MemberModule\Presenters;

use Listings\Components\IListingPdfGenerationControlFactory;
use Common\MemberModule\Presenters\SecuredPresenter;
use Listings\Components\ListingPdfGenerationControl;
use Common\Components\FlashMessages\FlashMessage;
use Joseki\Application\Responses\PdfResponse;
use blitzik\Authorization\Privilege;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Listings\Listing;

final class ListingPdfGenerationPresenter extends SecuredPresenter
{
    /**
     * @var IListingPdfGenerationControlFactory
     * @inject
     */
    public $listingPdfGenerationControlFactory;

    /**
     * @var ListingFacade
     * @inject
     */
    public $listingFacade;


    /** @var Listing */
    private $listing;


    public function actionDefault($id): void
    {
        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->withOwner()
                                  ->byId($id)
                              );

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::VIEW)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Výčetka - generování PDF');
        $this->setListingPageTitle($this->listing);
    }


    public function renderDefault($id): void
    {
    }


    protected function createComponentPdfGeneration(): ListingPdfGenerationControl
    {
        $comp = $this->listingPdfGenerationControlFactory
                     ->create($this->listing);

        $comp->onPdfGenerationClick[] = function (PdfResponse $pdfResponse) {
            $this->sendResponse($pdfResponse);
        };

        return $comp;
    }

}