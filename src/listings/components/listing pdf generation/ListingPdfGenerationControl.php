<?php

namespace Listings\Components;

use Listings\Queries\Factories\ListingItemQueryFactory;
use Joseki\Application\Responses\PdfResponse;
use Listings\Facades\ListingItemFacade;
use Listings\Facades\EmployerFacade;
use Nette\Application\UI\Multiplier;
use Listings\Facades\ListingFacade;
use Listings\Services\InvoiceTime;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\ListingItem;
use Listings\Listing;

class ListingPdfGenerationControl extends BaseControl
{
    public $onPdfGenerationClick;


    /** @var IPdfListingItemControlFactory */
    private $pdfListingItemControlFactory;

    /** @var ListingItemFacade */
    private $listingItemFacade;

    /** @var EmployerFacade */
    private $employerFacade;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var ListingItem[] */
    private $listingItems;

    /** @var Listing */
    private $listing;


    public function __construct(
        Listing $listing,
        ListingFacade $listingFacade,
        EmployerFacade $employerFacade,
        ListingItemFacade $listingItemFacade,
        IPdfListingItemControlFactory $pdfListingItemControlFactory
    ) {
        $this->listing = $listing;
        $this->listingFacade = $listingFacade;
        $this->employerFacade = $employerFacade;
        $this->listingItemFacade = $listingItemFacade;
        $this->pdfListingItemControlFactory = $pdfListingItemControlFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingPdfGeneration.latte');


        $template->render();
    }


    protected function createComponentPdfSettings()
    {
        $form = new Form;

        $form->addSelect('employer', 'Zaměstnavatel')
             ->setPrompt('Bez zaměstnavatele')
             ->setItems($this->employerFacade->findEmployersForSelect($this->listing->getOwnerId()));
        if ($this->listing->getEmployer() !== null) {
            $form['employer']->setDefaultValue($this->listing->getEmployer()->getId());
        }

        $form->addText('employee', 'Jméno', null, 70)
                ->setDefaultValue($this->listing->getOwnerFullName())
                ->addCondition(Form::FILLED)
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', 70);

        $form->addCheckbox('displayHourlyRate', 'Zobrazit "základní mzdu"')
             ->setDefaultValue(true);

        $form->addSubmit('generatePdf', 'Reset nastavení');

        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    protected function createComponentListingItem()
    {
        return new Multiplier(function ($day) {
            $item = null;
            if (isset($this->listingItems[$day])) {
                $item = $this->listingItems[$day];
            }
            $comp = $this->pdfListingItemControlFactory
                         ->create($day, $this->listing, $item);

            return $comp;
        });
    }


    public function processListing(Form $form, $values)
    {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/listing_templates/default.latte');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        $template->daysInMonth = $daysInMonth;

        $this->listingItems = $this->listingItemFacade
                             ->findListingItems(
                                 ListingItemQueryFactory::filterByListing($this->listing->getId())
                                 ->indexedByDay()
                             )->toArray();

        $template->listingItems = $this->listingItems;
        $template->listing = $this->listing;

        $listingData = $this->listingFacade->getWorkedDaysAndHours($this->listing);
        $template->totalWorkedDays = $listingData['daysCount'];
        $template->totalWorkedHoursInSeconds = new InvoiceTime($listingData['hoursInSeconds']);

        $employer = null;
        if ($values['employer'] !== null) {
            $employer = $this->employerFacade->getEmployer($values['employer']);
        }
        $template->employer = $employer;
        $template->employeeFullName = $values['employee'];

        $template->displayHourlyRate = $values['displayHourlyRate'];

        $pdf = new PdfResponse($template);
        $pdf->setPageMargins('10,10,10,10,0,0');
        $pdf->saveMode = PdfResponse::INLINE;


        $this->onPdfGenerationClick($pdf);
    }

}


interface IListingPdfGenerationControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingPdfGenerationControl
     */
    public function create(Listing $listing);
}