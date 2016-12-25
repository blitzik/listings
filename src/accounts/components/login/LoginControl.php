<?php

namespace Accounts\Components;

use App\Components\FlashMessages\FlashMessage;
use Nette\Security\AuthenticationException;
use App\Components\BaseControl;
use Nette\Application\UI\Form;

class LoginControl extends BaseControl
{
    public $onSuccessfulLogin;


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/login.latte');


        $template->render();
    }


    protected function createComponentLoginForm()
    {
        $form = new Form;

        $form->addText('email', 'E-mailová adresa')
                ->setRequired('Zadejte Váš %label');

        $form->addPassword('password', 'Heslo')
                ->setRequired('zadejte Vaše %label');

        $form->addSubmit('login', 'Přihlásit');

        $form->onSuccess[] = [$this, 'processCredentials'];


        return $form;
    }


    public function processCredentials(Form $form, $values)
    {
        try {
            $this->user->login($values['email'], $values['password']);
            $this->user->setExpiration('+14 days');

            $this->onSuccessfulLogin();

        } catch (AuthenticationException $e) {
            $this->flashMessage('Špatný E-mail nebo Heslo', FlashMessage::WARNING);
        }
    }
}


interface ILoginControlFactory
{
    /**
     * @return LoginControl
     */
    public function create();
}