<?php

namespace Listings\Components;

use Listings\Facades\ListingFacade;
use Listings\Services\TimeUtils;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\Listing;

class ListingFormControl extends BaseControl
{
    public $onSuccessfulSaving;


    /** @var ListingFacade */
    private $listingFacade;


    /** @var Listing|null */
    private $listing;


    public function __construct(
        ListingFacade $listingFacade
    ) {
        $this->listingFacade = $listingFacade;
    }


    /**
     * @param Listing $listing
     */
    public function setListing(Listing $listing)
    {
        $this->listing = $listing;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingForm.latte');




        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;

        $form->addSelect('month', 'Měsíc', TimeUtils::getMonths(true))
                ->setRequired();

        $form->addSelect('year', 'Rok', TimeUtils::generateYearsForSelection())
                ->setRequired();

        $form->addText('name', 'Název', null, Listing::LENGTH_NAME)
                ->setNullable();

        $form->addText('hourlyRate', 'Hodinová mzda')
                ->setNullable()
                ->addCondition(Form::FILLED)
                ->addRule(Form::INTEGER, 'Do pole %label lze vkládat pouze celá čísla')
                ->addRule(Form::MIN, 'Do pole %label lze vkládat pouze kladná čísla', 0);

        if ($this->listing !== null) {
            $this->prepareEditForm($form, $this->listing);
        }


        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'processForm'];


        return $form;
    }


    public function processForm(Form $form)
    {
        $values = $form->getValues(true);
        $values['owner'] = $this->user->getIdentity();

        $listing = $this->listingFacade->save($values, $this->listing);

        $this->onSuccessfulSaving($listing);
    }


    private function prepareEditForm(Form $form, Listing $listing)
    {
        $form['month']->setDisabled()
                      ->setDefaultValue($this->listing->getMonth());

        $form['year']->setDisabled()
                     ->setDefaultValue($this->listing->getYear());

        $form['name']->setDefaultValue($this->listing->getName());
        $form['hourlyRate']->setDefaultValue($this->listing->getHourlyRate());
    }
}


interface IListingFormControlFactory
{
    /**
     * @return ListingFormControl
     */
    public function create();
}