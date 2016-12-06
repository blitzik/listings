<?php

namespace Listings\Components;

use App\Components\BaseControl;
use Listings\Facades\ListingItemFacade;
use Listings\ListingItem;
use Nette\Application\UI\Form;

class ListingItemFormControl extends BaseControl
{
    /** @var ListingItemFacade */
    private $listingItemFacade;


    /** @var ListingItem|null */
    private $listingItem;


    public function __construct(
        ListingItemFacade $listingItemFacade
    ) {
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



        $template->render();
    }
    
    
    protected function createComponentForm()
    {
        $form = new Form;

        $form->addText('workStart', 'Začátek');
        $form->addText('workEnd', 'Konec');
        $form->addText('lunch', 'Oběd');
        $form->addText('locality', 'Místo pracoviště');

        $form->addSubmit('save', 'Uložit');

        return $form;
    }
}


interface IListingItemFormControlFactory
{
    /**
     * @return ListingItemFormControl
     */
    public function create();
}