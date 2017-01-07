<?php

namespace Listings\Components;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Template\Filters\InvoiceTimeWithCommaFilter;
use Listings\Services\SimpleLunchListingItemManipulator;
use Listings\Services\Factories\ListingItemFormFactory;
use App\Components\FlashMessages\FlashMessage;
use Nette\Application\UI\ITemplate;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\IListingItem;
use Listings\Listing;

class ListingItemFormControl extends BaseControl
{
    /** @var ListingItemFormFactory */
    protected $listingItemFormFactory;

    /** @var SimpleLunchListingItemManipulator */
    protected $listingItemManipulator;


    /** @var IListingItem|null */
    protected $listingItem;

    /** @var Listing */
    protected $listing;

    /** @var int */
    protected $day;


    public function __construct(
        int $day,
        Listing $listing,
        ListingItemFormFactory $listingItemFormFactory,
        SimpleLunchListingItemManipulator $listingItemManipulator
    ) {
        $this->day = $day;
        $this->listing = $listing;
        $this->listingItemFormFactory = $listingItemFormFactory;
        $this->listingItemManipulator = $listingItemManipulator;
    }


    /**
     * @param IListingItem $listingItem
     */
    public function setListingItem(IListingItem $listingItem)
    {
        if ($listingItem->getListingId() !== $this->listing->getId()) {
            throw new InvalidArgumentException;
        }
        $this->listingItem = $listingItem;
    }


    public function render()
    {
        $template = $this->getTemplate();

        $this->setPathToTemplate($template);
        $this->fillTemplate($template);


        $template->render();
    }


    /**
     * @param ITemplate $template
     */
    protected function setPathToTemplate(ITemplate $template)
    {
        $template->setFile(__DIR__ . '/listingItemForm.latte');
    }


    /**
     * @param ITemplate $template
     */
    protected function fillTemplate(ITemplate $template)
    {
        $template->listing = $this->listing;

        $template->date = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));

        $template->listingLocalities = $this->listingItemManipulator->loadLocalities($this->listing->getId());

        $workedHours = '9';
        if ($this->listingItem !== null) {
            $workedHours = InvoiceTimeWithCommaFilter::convert($this->listingItem->getWorkedHours());
        }
        $template->workedHours = $workedHours;
    }
    
    
    protected function createComponentForm()
    {
        $form = $this->listingItemFormFactory->create($this->listingItem);

        $form->addText('lunch', 'Oběd')
                ->setRequired('Zadejte délku oběda')
                ->setHtmlId('_work-lunch')
                ->setDefaultValue('1')
                ->addRule(Form::PATTERN, 'Špatný formát času', '^\d+(,[05])?$');

        if ($this->listingItem !== null) {
            $this->fillForm($form, $this->listingItem);
        }


        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form)
    {
        $values = $form->getValues(true);
        $values['listing'] = $this->listing;
        $values['day'] = $this->day;
        try {
            $this->listingItemManipulator->save((array)$values, $this->listingItem);

            $this->flashMessage('Položka byla uložena.', FlashMessage::SUCCESS);

        } catch (WorkedHoursRangeException $e) {
            $this->flashMessage('Pracovní doba nemůže skončit dříve, než začala.', FlashMessage::WARNING);

        } catch (NegativeWorkedTimeException $e) {
            $this->flashMessage('Položku nelze uložit. Musíte mít odpracováno více hodin, než kolik strávíte obědem.', FlashMessage::WARNING);
        }

        $this->redrawControl('flashMessages');
    }


    /**
     * @param Form $form
     * @param IListingItem $listingItem
     */
    protected function fillForm(Form $form, IListingItem $listingItem)
    {
        $form['lunch']->setDefaultValue(InvoiceTimeWithCommaFilter::convert($listingItem->getLunch()));
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