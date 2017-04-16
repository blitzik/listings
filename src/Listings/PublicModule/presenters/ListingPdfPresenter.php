<?php declare(strict_types=1);

namespace Listings\PublicModule\Presenters;

use Listings\Components\EmptyListingPdfGenerationControl;
use Listings\Components\IEmptyListingPdfGenerationControlFactory;
use Common\AuthModule\Presenters\PublicPresenter;
use Joseki\Application\Responses\PdfResponse;

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


    protected function createComponentPdfGeneration(): EmptyListingPdfGenerationControl
    {
        $comp = $this->emptyListingPdfGenerationControlFactory->create();

        $comp->onPdfGenerationClick[] = function (PdfResponse $pdfResponse) {
            $this->sendResponse($pdfResponse);
        };

        return $comp;
    }
}