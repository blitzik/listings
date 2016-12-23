<?php

namespace Listings\Components;

use Nette\Forms\Controls\SubmitButton;
use Listings\Facades\ListingFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\Listing;

class ListingRemovalControl extends BaseControl
{
    public $onSuccessfulRemoval;
    public $onCancelClick;

    /** @var ListingFacade */
    private $listingFacade;


    /** @var Listing */
    private $listing;


    public function __construct(
        Listing $listing,
        ListingFacade $listingFacade
    ) {
        $this->listing = $listing;
        $this->listingFacade = $listingFacade;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingRemoval.latte');



        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form();

        $form->addSubmit('remove', 'Zrušit výčetku')
                ->onClick[] = [$this, 'processRemove'];

        $form->addSubmit('cancel', 'Zpět')
                ->onClick[] = [$this, 'processCancel'];

        //$form->addProtection();


        return $form;
    }


    public function processRemove(SubmitButton $button)
    {
        $this->listingFacade->remove($this->listing);

        $this->onSuccessfulRemoval();
    }


    public function processCancel(SubmitButton $button)
    {
        $this->onCancelClick();
    }
}


interface IListingRemovalControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingRemovalControl
     */
    public function create(Listing $listing);
}