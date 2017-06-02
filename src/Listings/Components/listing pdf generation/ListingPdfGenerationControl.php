<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Services\ListingItemManipulatorFactory;
use Listings\Services\IListingItemManipulator;
use Joseki\Application\Responses\PdfResponse;
use Listings\Pdf\ListingPdfTemplateFactory;
use Listings\Facades\EmployerFacade;
use Listings\Facades\ListingFacade;
use Common\Components\BaseControl;
use Listings\Pdf\ListingPdfDTO;
use Nette\Application\UI\Form;
use Listings\Listing;

class ListingPdfGenerationControl extends BaseControl
{
    public $onPdfGenerationClick;


    const TYPE_DEFAULT = 'default';


    /** @var ListingPdfTemplateFactory */
    private $listingPdfTemplateFactory;

    /** @var IListingItemManipulator */
    private $listingItemManipulator;

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
        ListingItemManipulatorFactory $listingItemManipulatorFactory,
        ListingPdfTemplateFactory $listingPdfTemplateFactory
    ) {
        $this->listing = $listing;
        $this->listingFacade = $listingFacade;
        $this->employerFacade = $employerFacade;
        $this->listingItemManipulator = $listingItemManipulatorFactory->getByListing($listing);
        $this->listingPdfTemplateFactory = $listingPdfTemplateFactory;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingPdfGeneration.latte');


        $template->render();
    }


    protected function createComponentPdfSettings(): Form
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


    public function processListing(Form $form, $values): void
    {
        $this->listingItemPdfType = $values['template'];

        $listingItems = $this->listingItemManipulator->findListingItems($this->listing->getId());

        $pdfDto = new ListingPdfDTO($this->listing->getYear(), $this->listing->getMonth(), $this->listing->getItemsType());
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
    public function create(Listing $listing): ListingPdfGenerationControl;
}