<?php

namespace Accounts\Components;

use Accounts\Exceptions\Runtime\EmailSendingFailedException;
use Accounts\Exceptions\Runtime\UserNotFoundException;
use App\Components\FlashMessages\FlashMessage;
use Accounts\Facades\AccountFacade;
use App\Components\BaseControl;
use Nette\Application\UI\Form;

class ForgottenPasswordFormControl extends BaseControl
{
    /** @var AccountFacade */
    private $accountFacade;


    /** @var string */
    private $applicationUrl;

    /** @var string */
    private $adminFullName;

    /** @var string */
    private $adminEmail;


    public function __construct(
        AccountFacade $accountFacade
    ) {
        $this->accountFacade = $accountFacade;
    }


    /**
     * @param string $applicationUrl
     */
    public function setApplicationUrl($applicationUrl)
    {
        $this->applicationUrl = $applicationUrl;
    }


    /**
     * @param string $adminFullName
     */
    public function setAdminFullName($adminFullName)
    {
        $this->adminFullName = $adminFullName;
    }


    /**
     * @param string $email
     */
    public function setAdminEmail($email)
    {
        $this->adminEmail = $email;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/forgottenPasswordForm.latte');



        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;
        $form->getElementPrototype()->class = 'ajax';

        $form->addText('email', 'E-mail')
                ->setRequired('Zadejte Váš E-mail')
                ->setAttribute('Zadejte Vaši E-mailovou adresu')
                ->addRule(Form::EMAIL, 'Zadejte správný formát E-mailu');


        $form->addSubmit('process', 'Obnovit heslo');


        $form->onSuccess[] = [$this, 'processForm'];


        return $form;
    }


    public function processForm(Form $form, $values)
    {
        try {
            $this->accountFacade
                ->restorePassword(
                    $values['email'],
                    $this->adminEmail,
                    $this->applicationUrl,
                    $this->adminFullName
                );

            $this->flashMessage('Na Váš E-mail byly zaslány instrukce pro obnovení hesla', FlashMessage::SUCCESS);
            $this->refresh('this', ['flashMessages', 'form']);
            unset($this['form']);

        } catch (UserNotFoundException $e) {
            $this->flashMessage('Zadaný E-mail se v systému nenachází', FlashMessage::WARNING);
            $this->refresh('this', ['flashMessages']);

        } catch (EmailSendingFailedException $e) {
            $this->flashMessage('Obnova hesla selhala. Zkuste akci opakovat později.', FlashMessage::ERROR);
            $this->refresh('this', ['flashMessages']);
        }
    }
}


interface IForgottenPasswordFormControlFactory
{
    /**
     * @return ForgottenPasswordFormControl
     */
    public function create();
}