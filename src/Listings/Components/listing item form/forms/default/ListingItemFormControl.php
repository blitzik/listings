<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Template\Filters\InvoiceTimeWithCommaFilter;
use Listings\Services\SimpleLunchListingItemManipulator;
use Listings\Services\Factories\ListingItemFormFactory;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Common\Components\FlashMessages\FlashMessage;
use Nette\Application\UI\ITemplate;
use Common\Components\BaseControl;
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


    public function setListingItem(IListingItem $listingItem): void
    {
        if ($listingItem->getListingId() !== $this->listing->getId()) {
            throw new InvalidArgumentException;
        }
        $this->listingItem = $listingItem;
    }


    public function render(): void
    {
        $template = $this->getTemplate();

        $this->setPathToTemplate($template);
        $this->fillTemplate($template);


        $template->render();
    }


    protected function setPathToTemplate(ITemplate $template): void
    {
        $template->setFile(__DIR__ . '/listingItemForm.latte');
    }


    protected function fillTemplate(ITemplate $template) : void
    {
        $template->listing = $this->listing;

        $template->date = \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day));

        $template->listingLocalities = $this->listingItemManipulator->loadLocalities($this->listing->getId());

        $workedHours = $this->listing->getDefaultSettings()->getWorkedHours()->getTimeWithComma();
        if ($this->listingItem !== null) {
            $workedHours = $this->listingItem->getWorkedHours()->getTimeWithComma();
        }
        $template->workedHours = $workedHours;
    }
    
    
    protected function createComponentForm(): Form
    {
        $form = $this->listingItemFormFactory->create($this->listing->getDefaultSettings(), $this->listingItem);

        $form->addText('lunch', 'Oběd')
                ->setRequired('Zadejte délku oběda')
                ->setHtmlId('_work-lunch')
                ->setDefaultValue($this->listing->getDefaultSettings()->getLunchHours()->getTimeWithComma())
                ->addRule(Form::PATTERN, 'Špatný formát času', '^\d+(,[05])?$');

        if ($this->listingItem !== null) {
            $this->fillForm($form, $this->listingItem);
        }


        $form->onSuccess[] = [$this, 'processListing'];


        return $form;
    }


    public function processListing(Form $form): void
    {
        $values = $form->getValues(true);
        $values['listing'] = $this->listing;
        $values['day'] = $this->day;
        try {
            $this->listingItemManipulator->save((array)$values, $this->listingItem);

            $this->flashMessage('Položka byla uložena.', FlashMessage::SUCCESS);

        } catch (WorkedHoursRangeException $e) {
            $this->flashMessage('Položku nelze uložit. Pracovní doba nemůže skončit dříve, než začala.', FlashMessage::WARNING);

        } catch (WorkedHoursException $e) {
            $this->flashMessage('Položku nelze uložit. Musíte mít odpracováno alespoň 30 minut.', FlashMessage::WARNING);
        }

        $this->redrawControl('flashMessages2');
    }


    protected function fillForm(Form $form, IListingItem $listingItem): void
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
    public function create(int $day, Listing $listing): ListingItemFormControl;
}