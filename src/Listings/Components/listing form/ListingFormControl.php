<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Facades\EmployerFacade;
use Listings\Facades\ListingFacade;
use Common\Components\BaseControl;
use Listings\Services\TimeUtils;
use Nette\Application\UI\Form;
use Listings\ListingSettings;
use Listings\Listing;

class ListingFormControl extends BaseControl
{
    public $onSuccessfulSaving;


    /** @var EmployerFacade */
    private $employerFacade;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var ListingSettings */
    private $listingSettings;

    /** @var Listing|null */
    private $listing;


    public function __construct(
        ListingFacade $listingFacade,
        EmployerFacade $employerFacade
    ) {
        $this->listingFacade = $listingFacade;
        $this->employerFacade = $employerFacade;
    }


    /**
     * @param Listing $listing
     */
    public function setListing(Listing $listing): void
    {
        $this->listing = $listing;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingForm.latte');


        $template->render();
    }


    protected function createComponentForm(): Form
    {
        $form = new Form;

        $form->addSelect('month', 'Měsíc', TimeUtils::getMonths(true))
                ->setRequired()
                ->setDefaultValue(date('n'));

        $form->addSelect('year', 'Rok', TimeUtils::generateYearsForSelection())
                ->setRequired()
                ->setDefaultValue(date('Y'));

        $form->addSelect('employer', 'Zaměstnavatel')
             ->setPrompt('Bez zaměstnavatele')
             ->setItems($this->employerFacade->findEmployersForSelect($this->user->getId()));

        $form->addText('name', 'Název', null, Listing::LENGTH_NAME)
                ->setNullable()
                ->addCondition(Form::FILLED)
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', Listing::LENGTH_NAME);

        $itemTypes = Listing::getTypes();
        $form->addSelect('itemType', 'Typ položek')
                ->setItems($itemTypes);
        if ($this->listing === null and $this->listingSettings === null) {
            $this->listingSettings = $this->listingFacade->getListingSettings($this->user->getIdentity());
            $form['itemType']->setDefaultValue($this->listingSettings->getItemType());
        }

        $form->addText('hourlyRate', 'Hodinová mzda')
                ->setNullable()
                ->addCondition(Form::FILLED)
                ->addRule(Form::INTEGER, 'Do pole %label lze vkládat pouze celá čísla')
                ->addRule(Form::MIN, 'Do pole %label lze vkládat pouze kladná čísla', 0);

        if ($this->listing !== null) {
            $this->prepareEditForm($form, $this->listing);
        }


        $form->addSubmit('save', 'Uložit');

        $form->addProtection();

        $form->onSuccess[] = [$this, 'processForm'];


        return $form;
    }


    public function processForm(Form $form): void
    {
        $values = $form->getValues(true);
        $values['owner'] = $this->user->getIdentity();

        $listing = $this->listingFacade->save($values, $this->listing);

        $this->onSuccessfulSaving($listing);
    }


    private function prepareEditForm(Form $form, Listing $listing): void
    {
        $form['month']->setDisabled()
                      ->setDefaultValue($this->listing->getMonth());

        $form['year']->setDisabled()
                     ->setDefaultValue($this->listing->getYear());

        $form['itemType']->setDisabled()
                         ->setDefaultValue($this->listing->getItemsType());

        $form['employer']->setDefaultValue($this->listing->getEmployerId());

        $form['name']->setDefaultValue($this->listing->getName());
        $form['hourlyRate']->setDefaultValue($this->listing->getHourlyRate());
    }

}


interface IListingFormControlFactory
{
    /**
     * @return ListingFormControl
     */
    public function create(): ListingFormControl;
}