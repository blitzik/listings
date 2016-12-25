<?php

namespace Accounts\Components;

use Accounts\Facades\AccountFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;

class RegistrationControl extends BaseControl
{
    /** @var AccountFacade */
    private $accountFacade;


    public function __construct(
        AccountFacade $accountFacade
    ) {
        $this->accountFacade = $accountFacade;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/registration.latte');




        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;

        $form->addText('email', 'E-mailová adresa');

        $form->addText('firstName', 'Jméno');

        $form->addText('lastName', 'Příjmení');

        $form->addPassword('pass', 'Heslo');

        $form->addPassword('passCheck', 'Heslo znovu');


        $form->addSubmit('save', 'Vytvořit účet');


        return $form;
    }
}


interface IRegistrationControlFactory
{
    /**
     * @return RegistrationControl
     */
    public function create();
}