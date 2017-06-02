<?php declare(strict_types=1);

namespace Listings\Components;

use Listings\Facades\ListingFacade;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Listings\Listing;

class ListingRemovalControl extends BaseControl
{
    public $onSuccessfulRemoval;


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


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/listingRemoval.latte');

        $template->listing = $this->listing;



        $template->render();
    }


    protected function createComponentForm(): Form
    {
        $form = new Form;

        $form->addText('confirmation', 'Zadejte kontrolní text "odstranit". Bez uvozovek.')
                ->setRequired('Zadejte kontrolní text')
                ->addRule(Form::EQUAL, 'Neshoduje se kontrolní text.', 'odstranit');

        $form->addSubmit('remove', 'Odstranit výčetku');

        $form->onSuccess[] = [$this, 'processRemoval'];

        $form->addProtection();


        return $form;
    }


    public function processRemoval(Form $form, $values): void
    {
        $this->listingFacade->remove($this->listing);

        $this->onSuccessfulRemoval();
    }
}


interface IListingRemovalControlFactory
{
    /**
     * @param Listing $listing
     * @return ListingRemovalControl
     */
    public function create(Listing $listing): ListingRemovalControl;
}