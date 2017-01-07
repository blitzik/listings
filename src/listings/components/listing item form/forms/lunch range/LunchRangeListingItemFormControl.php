<?php

namespace Listings\Components;

use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Listings\Services\Factories\ListingItemFormFactory;
use Listings\Services\RangeLunchListingItemManipulator;
use Listings\Template\Filters\InvoiceTimeFilter;
use App\Components\FlashMessages\FlashMessage;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Form;
use Listings\IListingItem;
use Listings\Listing;

class LunchRangeListingItemFormControl extends ListingItemFormControl
{
    public function __construct(
        int $day,
        Listing $listing,
        ListingItemFormFactory $listingItemFormFactory,
        RangeLunchListingItemManipulator $listingItemManipulator
    ) {
        $this->day = $day;
        $this->listing = $listing;
        $this->listingItemFormFactory = $listingItemFormFactory;
        $this->listingItemManipulator = $listingItemManipulator;
    }


    /**
     * @param ITemplate $template
     */
    protected function setPathToTemplate(ITemplate $template)
    {
        $template->setFile(__DIR__ . '/listingItemForm.latte');
    }


    protected function createComponentForm()
    {
        $form = $this->listingItemFormFactory->create($this->listingItem);

        $form->addText('lunchStart', 'Oběd začátek')
                ->setRequired('Zadejte začátek oběda')
                ->setHtmlId('_work-lunch-start')
                ->setDefaultValue('11:00')
                ->addRule(Form::PATTERN, 'Špatný formát času', $this->listingItemFormFactory->getTimeRegex());

        $form->addText('lunchEnd', 'Oběd konec')
            ->setRequired('Zadejte konec oběda')
            ->setHtmlId('_work-lunch-end')
            ->setDefaultValue('12:00')
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->listingItemFormFactory->getTimeRegex());

        if ($this->listingItem !== null) {
            $this->fillForm($form, $this->listingItem);
        }


        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form)
    {
        try {
            parent::processListing($form);

        } catch (LunchHoursRangeException $e) {
            $this->flashMessage('Začátek a konec oběda se musí nacházet v rozsahu směny.', FlashMessage::WARNING);
        }

        $this->redrawControl('flashMessages');
    }


    protected function fillForm(Form $form, IListingItem $listingItem)
    {
        $form['lunchStart']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getLunchStart()));
        $form['lunchEnd']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getLunchEnd()));
    }


}


interface ILunchRangeListingItemFormControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @return LunchRangeListingItemFormControl
     */
    public function create(int $day, Listing $listing);
}