<?php

namespace Listings\Components;

use Joseki\Application\Responses\PdfResponse;
use Listings\Listing;
use Listings\Pdf\ListingPdfTemplateFactory;
use Listings\Pdf\ListingPdfDTO;
use Listings\Services\TimeUtils;
use App\Components\BaseControl;
use Nette\Application\UI\Form;

class EmptyListingPdfGenerationControl extends BaseControl
{
    public $onPdfGenerationClick;


    /** @var ListingPdfTemplateFactory */
    private $pdfTemplateFactory;


    public function __construct(
        ListingPdfTemplateFactory $pdfTemplateFactory
    ) {
        $this->pdfTemplateFactory = $pdfTemplateFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/emptyListingPdfGeneration.latte');



        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;

        $form->addRadioList('year', 'Zvolte rok:', $this->generateYears())
                ->setRequired('Zvolte rok')
                ->setDefaultValue(date('Y'));

        $form->addCheckboxList('months', null, TimeUtils::getMonths())
                ->setRequired('Vyberte měsíc')
                ->setDefaultValue(date('n'));

        $form->addSelect('template', 'Zvolte vzhled', [
            ListingPdfTemplateFactory::LAYOUT_DEFAULT => 'Základní šablona',
            ListingPdfTemplateFactory::LAYOUT_SEP => 'Šablona pro OSVČ'
        ]);

        $form->addSelect('itemType', 'Typ položek', Listing::getTypes());

        $form->addSubmit('generate', 'Vygenerovat PDF');

        $form->onSuccess[] = [$this, 'generatePdfs'];


        return $form;
    }


    public function generatePdfs(Form $form, $values)
    {
        $pdfDTOs = [];
        foreach ($values['months'] as $month) {
            $pdfDTOs[] = new ListingPdfDTO((int)$values['year'], (int)$month, (int)$values['itemType']);
        }

        $template = $this->pdfTemplateFactory
                         ->create($values['template'], ListingPdfTemplateFactory::ITEM_DEFAULT, $pdfDTOs);

        $pdf = new PdfResponse($template);
        $pdf->setPageMargins('10,10,10,10,0,0');
        $pdf->saveMode = PdfResponse::INLINE;

        $this->onPdfGenerationClick($pdf);
    }


    private function generateYears()
    {
        $currentYear = date('Y');
        $years = array_reverse(range($currentYear - 3, $currentYear));
        $result = array_combine($years, $years);

        return $result;
    }
}


interface IEmptyListingPdfGenerationControlFactory
{
    /**
     * @return EmptyListingPdfGenerationControl
     */
    public function create();
}