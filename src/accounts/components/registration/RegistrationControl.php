<?php

namespace Accounts\Components;

use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Accounts\Services\Factories\AccountFormFactory;
use Accounts\Facades\AccountFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Users\User;

class RegistrationControl extends BaseControl
{
    public $onSuccessfulAccountCreation;


    /** @var AccountFormFactory */
    private $accountFormFactory;

    /** @var AccountFacade */
    private $accountFacade;


    public function __construct(
        AccountFacade $accountFacade,
        AccountFormFactory $accountFormFactory
    ) {
        $this->accountFacade = $accountFacade;
        $this->accountFormFactory = $accountFormFactory;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/registration.latte');




        $template->render();
    }


    protected function createComponentForm()
    {
        $form = $this->accountFormFactory->create();

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