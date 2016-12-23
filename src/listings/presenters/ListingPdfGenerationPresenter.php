<?php

namespace Listings\Presenters;

use Joseki\Application\Responses\PdfResponse;
use Listings\Components\IListingPdfGenerationControlFactory;
use App\MemberModule\Presenters\SecuredPresenter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingFacade;
use Listings\Queries\ListingQuery;
use Users\Authorization\Privilege;
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


    public function actionDefault($id)
    {
        $this->listing = $this->listingFacade
                              ->getListing(
                                  (new ListingQuery())
                                  ->byId($id)
                              );

        if ($this->listing === null or !$this->authorizator->isAllowed($this->user, $this->listing, Privilege::VIEW)) {
            $this->flashMessage('Požadovaná výčetka nebyla nalezena.', FlashMessage::WARNING);
            $this->redirect(':Listings:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Výčetka - generování PDF');
        $this->setListingPageTitle($this->listing);
    }


    public function renderDefault($id)
    {

    }


    protected function createComponentPdfGeneration()
    {
        $comp = $this->listingPdfGenerationControlFactory->create($this->listing);

        $comp->onPdfGenerationClick[] = function (PdfResponse $pdfResponse) {
            $this->sendResponse($pdfResponse);
        };

        return $comp;
    }

}