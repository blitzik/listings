<?php

namespace Listings\Components;

use Listings\Queries\Factories\ListingItemQueryFactory;
use Joseki\Application\Responses\PdfResponse;
use Listings\Pdf\ListingPdfTemplateFactory;
use Listings\Facades\ListingItemFacade;
use Listings\Facades\EmployerFacade;
use Listings\Facades\ListingFacade;
use Listings\Pdf\ListingPdfDTO;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\ListingItem;
use Listings\Listing;

class ListingPdfGenerationControl extends BaseControl
{
    public $onPdfGenerationClick;


    const TYPE_DEFAULT = 'default';


    /** @var ListingPdfTemplateFactory */
    private $listingPdfTemplateFactory;

    /** @var ListingItemFacade */
    private $listingItemFacade;

    /** @var EmployerFacade */
    private $employerFacade;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var string */
    private $listingItemPdfType;

    /** @var Listing */
    private $listing;


    public function __construct(
        Listing $listing,
        ListingFacade $listingFacade,
        EmployerFacade $employerFacade,
        ListingItemFacade $listingItemFacade,
        ListingPdfTemplateFactory $listingPdfTemplateFactory
    ) {
        $this->listing = $listing;
        $this->listingFacade = $listingFacade;
        $this->employerFacade = $employerFacade;
        $this->listingItemFacade = $listingItemFacade;
        $this->listingPdfTemplateFactory = $listingPdfTemplateFactory;
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
        if ($this->listing->hasSetEmployer()) {
            $form['employer']->setDefaultValue($this->listing->getEmployerId());
        }

        $form->addText('employee', 'Jméno', null, 70)
                ->setNullable()
                ->setDefaultValue($this->listing->getOwnerFullName())
                ->addCondition(Form::FILLED)
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', 70);


        $form->addCheckbox('displayHourlyRate', 'Zobrazit "základní mzdu"')
             ->setDefaultValue(true);

        $form->addSelect('template', 'Zvolte vzhled', [
            ListingPdfTemplateFactory::LAYOUT_DEFAULT => 'Základní šablona',
            ListingPdfTemplateFactory::LAYOUT_SEP => 'Šablona pro OSVČ'
        ]);


        $form->addSubmit('generatePdf', 'Reset nastavení');

        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form, $values)
    {
        $this->listingItemPdfType = $values['template'];

        $listingItems = $this->listingItemFacade
                             ->findListingItems(
                                 ListingItemQueryFactory::filterByListing($this->listing->getId())
                                 ->indexedByDay()
                             )->toArray();

        $pdfDto = new ListingPdfDTO($this->listing->getYear(), $this->listing->getMonth());
        $pdfDto->fillByListing($this->listing, $listingItems);

        $pdfDto->setEmployeeFullName($values['employee']);
        if ($values['displayHourlyRate']) {
            $pdfDto->displayHourlyRate();
        }

        $employer = null;
        if ($values['employer'] !== null) {
            $employer = $this->employerFacade->getEmployer($values['employer']);
            $pdfDto->setEmployerName($employer->getName());
        } else {
            $pdfDto->setEmployerName(null);
        }

        $template = $this->listingPdfTemplateFactory
                         ->create($values['template'], ListingPdfTemplateFactory::ITEM_DEFAULT, [$pdfDto]);

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