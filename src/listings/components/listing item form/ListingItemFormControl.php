<?php

namespace Listings\Components;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Template\Filters\InvoiceTimeWithCommaFilter;
use Listings\Template\Filters\InvoiceTimeFilter;
use App\Components\FlashMessages\FlashMessage;
use Listings\Facades\ListingItemFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\ListingItem;
use Listings\Listing;

class ListingItemFormControl extends BaseControl
{
    /** @var ListingItemFacade */
    private $listingItemFacade;


    /** @var ListingItem|null */
    private $listingItem;

    /** @var Listing */
    private $listing;

    /** @var int */
    private $day;


    public function __construct(
        int $day,
        Listing $listing,
        ListingItemFacade $listingItemFacade
    ) {
        $this->day = $day;
        $this->listing = $listing;
        $this->listingItemFacade = $listingItemFacade;
    }


    /**
     * @param ListingItem $listingItem
     */
    public function setListingItem(ListingItem $listingItem)
    {
        $this->listingItem = $listingItem;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingItemForm.latte');

        $template->listing = $this->listing;

        $template->date = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));

        $template->listingLocalities = $this->listingItemFacade->loadLocalities($this->listing->getId());

        $template->render();
    }
    
    
    protected function createComponentForm()
    {
        $form = new Form;
        $form->getElementPrototype()->class = 'ajax';
        //$form->getElementPrototype()->novalidate = 'novalidate';

        $form->addText('workStart', 'Začátek')
                ->setRequired()
                ->setHtmlId('_work-start')
                ->setDefaultValue('6:00')
                ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('workEnd', 'Konec')
                ->setRequired()
                ->setHtmlId('_work-end')
                ->setDefaultValue('16:00')
                ->addRule(Form::PATTERN, 'Špatný formát času', $this->getTimeRegex());

        $form->addText('lunch', 'Oběd')
                ->setRequired()
                ->setHtmlId('_work-lunch')
                ->setDefaultValue('1')
                ->addRule(Form::PATTERN, 'Špatný formát času', '^\d+(,[05])?$');

        $form->addText('workedHours', 'Odpr. hod.')
                ->setHtmlId('_work-worked-hours')
                ->setDisabled()
                ->setDefaultValue('9');

        $form->addText('locality', 'Místo pracoviště')
                ->setRequired('Zadejte místo pracoviště')
                ->setAttribute('list', '_work-locality');

        if ($this->listingItem !== null) {
            $this->fillForm($form, $this->listingItem);
        }

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form)
    {
        $values = $form->getValues(true);
        $values['listing'] = $this->listing;
        $values['day'] = $this->day;
        try {
            $this->listingItemFacade->saveListingItem((array)$values, $this->listingItem);

            $this->flashMessage('Položka byla uložena.', FlashMessage::SUCCESS);

        } catch (WorkedHoursRangeException $e) {
            $this->flashMessage('Pracovní doba nemůže skončit dříve, než začala.', FlashMessage::WARNING);

        } catch (NegativeWorkedTimeException $e) {
            $this->flashMessage('Položku nelze uložit. Musíte mít odpracováno více hodin, než kolik strávíte obědem.', FlashMessage::WARNING);
        }

        $this->redrawControl('flashMessages');
    }


    private function fillForm(Form $form, ListingItem $listingItem)
    {
        $form['workStart']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkStart(), true));
        $form['workEnd']->setDefaultValue(InvoiceTimeFilter::convert($listingItem->getWorkEnd(), true));
        $form['lunch']->setDefaultValue(InvoiceTimeWithCommaFilter::convert($listingItem->getLunch()));
        $form['workedHours']->setDefaultValue(InvoiceTimeWithCommaFilter::convert($listingItem->getWorkedHours()));

        $form['locality']->setDefaultValue($listingItem->getLocality());
    }


    private function getTimeRegex()
    {
        return '^(0?[0-9]|1[0-9]|2[0-3]):[03]0$';
    }

}


interface IListingItemFormControlFactory
{
    /**
     * @param int $day
     * @param Listing $listing
     * @return ListingItemFormControl
     */
    public function create(int $day, Listing $listing);
}