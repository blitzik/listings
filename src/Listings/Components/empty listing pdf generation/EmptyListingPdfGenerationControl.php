<?php declare(strict_types=1);

namespace Listings\Components;

use Joseki\Application\Responses\PdfResponse;
use Listings\Pdf\ListingPdfTemplateFactory;
use Common\Components\BaseControl;
use Listings\Services\TimeUtils;
use Listings\Pdf\ListingPdfDTO;
use Nette\Application\UI\Form;
use Listings\Listing;

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


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/emptyListingPdfGeneration.latte');



        $template->render();
    }


    protected function createComponentForm(): Form
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


    public function generatePdfs(Form $form, $values): void
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


    private function generateYears(): array
    {
        $currentYear = date('Y') + 1;
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
    public function create(): EmptyListingPdfGenerationControl;
}