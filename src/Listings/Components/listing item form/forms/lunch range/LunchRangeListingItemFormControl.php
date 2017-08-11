<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Listings\Services\Factories\ListingItemFormFactory;
use Listings\Services\RangeLunchListingItemManipulator;
use Listings\Exceptions\Runtime\LunchHoursException;
use Common\Components\FlashMessages\FlashMessage;
use Listings\Template\Filters\InvoiceTimeFilter;
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


    protected function setPathToTemplate(ITemplate $template): void
    {
        $template->setFile(__DIR__ . '/listingItemForm.latte');
    }


    protected function createComponentForm(): Form
    {
        $form = $this->listingItemFormFactory->create($this->listing->getDefaultSettings(), $this->listingItem);

        $form->addText('lunchStart', 'Oběd začátek')
                ->setRequired('Zadejte začátek oběda')
                ->setHtmlId('_work-lunch-start')
                ->setDefaultValue(InvoiceTimeFilter::convert($this->listing->getDefaultSettings()->getLunchStart(), true))
                ->addRule(Form::PATTERN, 'Špatný formát času', $this->listingItemFormFactory->getTimeRegex());

        $form->addText('lunchEnd', 'Oběd konec')
            ->setRequired('Zadejte konec oběda')
            ->setHtmlId('_work-lunch-end')
            ->setDefaultValue(InvoiceTimeFilter::convert($this->listing->getDefaultSettings()->getLunchEnd(), true))
            ->addRule(Form::PATTERN, 'Špatný formát času', $this->listingItemFormFactory->getTimeRegex());

        if ($this->listingItem !== null) {
            $this->fillForm($form, $this->listingItem);
        }


        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form): void
    {
        try {
            parent::processListing($form);

        } catch (LunchHoursRangeException $e) {
            $this->flashMessage('Položku nelze uložit. Oběd nemůže končit dříve než začal.', FlashMessage::WARNING);

        } catch (LunchHoursException $e) {
            $this->flashMessage('Položku nelze uložit. Začátek a konec oběda se musí nacházet v rozsahu směny.', FlashMessage::WARNING);
        }

        $this->redrawControl('flashMessages');
        $this->redrawControl('flashMessages2');
    }


    protected function fillForm(Form $form, IListingItem $listingItem): void
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
    public function create(int $day, Listing $listing): LunchRangeListingItemFormControl;
}