<?php

namespace Accounts\Components;

use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Accounts\Facades\AccountFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Users\User;

class RegistrationControl extends BaseControl
{
    public $onSuccessfulAccountCreation;


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
        $form->getElementPrototype()->class = 'ajax';

        $form->addText('email', 'E-mailová adresa', null, User::LENGTH_EMAIL)
                ->setRequired('Zadejte E-mailovou adresu')
                ->addRule(Form::EMAIL, '%label nemá platný formát')
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', User::LENGTH_EMAIL);

        $form->addText('firstName', 'Jméno', null, User::LENGTH_FIRSTNAME)
                ->setRequired('Zadejte Vaše jméno')
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', User::LENGTH_FIRSTNAME);

        $form->addText('lastName', 'Příjmení', null, User::LENGTH_LASTNAME)
                ->setRequired('Zadejte Vaše příjmení')
                ->addRule(Form::MAX_LENGTH, 'Lze zadat max. %d znaků', User::LENGTH_LASTNAME);

        $form->addPassword('pass', 'Heslo')
                ->setRequired('Zadejte Vaše heslo')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 5);

        $form->addPassword('passCheck', 'Heslo znovu')
                ->setRequired('Zadejte Vaše heslo znovu do kontrolního pole')
                ->setOmitted()
                ->addRule(Form::EQUAL, 'Zadaná hesla se musí shodovat', $form['pass']);


        $form->addSubmit('save', 'Vytvořit účet');


        $form->onSuccess[] = [$this, 'processForm'];
        

        return $form;
    }


    public function processForm(Form $form, $values)
    {
        try {
            $this->accountFacade->createAccount((array)$values);

            $this->onSuccessfulAccountCreation();

        } catch (EmailIsInUseException $e) {
            $form['email']->addError('Zadaný E-mail je již využíván jiným uživatelem');
        }

        $this->redrawControl('form');
    }
}


interface IRegistrationControlFactory
{
    /**
     * @return RegistrationControl
     */
    public function create();
}