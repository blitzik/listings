<?php declare(strict_types = 1);

namespace Listings\Services\Factories;

use Listings\Template\Filters\InvoiceTimeFilter;
use Nette\Application\UI\Form;
use Listings\ListingSettings;
use Listings\Listing;
use Nette\SmartObject;

class ListingSettingsFormFactory
{
    use SmartObject;


    public function create(ListingSettings $settings): Form
    {
        $form = new Form;

        $form->addSelect('itemType', 'Zvolte typ položek výčetky:', Listing::getTypes())
                ->setDefaultValue($settings->getItemType());

        $form->addText('workStart', 'Začátek směny')
                ->setRequired('Zadejte začátek směny')
                ->setHtmlId('_work-start')
                ->setDefaultValue(InvoiceTimeFilter::convert($settings->getWorkStart(), true))
                ->addRule(Form::PATTERN, 'Pole "%label" obsahuje špatný formát času.', $this->getHoursAndMinutesRegexp());

        $form->addText('workEnd', 'Konec směny')
                ->setRequired('Zadejte konec směny')
                ->setHtmlId('_work-end')
                ->setDefaultValue(InvoiceTimeFilter::convert($settings->getWorkEnd(), true))
                ->addRule(Form::PATTERN, 'Pole "%label" obsahuje špatný formát času.', $this->getHoursAndMinutesRegexp());

        $form->addText('lunchStart', 'Začátek oběda')
                ->setRequired('Zadejte začátek oběda')
                ->setHtmlId('_work-lunch-start')
                ->setDefaultValue(InvoiceTimeFilter::convert($settings->getLunchStart(), true))
                ->addRule(Form::PATTERN, 'Pole "%label" obsahuje špatný formát času.', $this->getHoursAndMinutesRegexp());

        $form->addText('lunchEnd', 'Konec oběda')
                ->setRequired('Zadejte konec oběda')
                ->setHtmlId('_work-lunch-end')
                ->setDefaultValue(InvoiceTimeFilter::convert($settings->getLunchEnd(), true))
                ->addRule(Form::PATTERN, 'Pole "%label" obsahuje špatný formát času.', $this->getHoursAndMinutesRegexp());


        $form->addSubmit('save', 'Uložit');


        return $form;
    }


    private function getHoursAndMinutesRegexp(): string
    {
        return '^-?\d+:[0-5][0-9]$';
    }
}