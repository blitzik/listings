<?php

namespace Listings\PublicModule\Presenters;

use Joseki\Application\Responses\PdfResponse;
use Listings\Components\IEmptyListingPdfGenerationControlFactory;
use App\AuthModule\Presenters\PublicPresenter;

final class ListingPdfPresenter extends PublicPresenter
{
    /**
     * @var IEmptyListingPdfGenerationControlFactory
     * @inject
     */
    public $emptyListingPdfGenerationControlFactory;


    public function actionDefault()
    {
        $this['metaTitle']->setTitle('Generování prázdných výčetek');
    }


    public function renderDefault()
    {

    }


    protected function createComponentPdfGeneration()
    {
        $comp = $this->emptyListingPdfGenerationControlFactory->create();

        $comp->onPdfGenerationClick[] = function (PdfResponse $pdfResponse) {
            $this->sendResponse($pdfResponse);
        };

        return $comp;
    }
}