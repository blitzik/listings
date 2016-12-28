<?php

declare(strict_types=1);

namespace Accounts\Services\Factories;

use Nette\Application\UI\Form;
use Nette\SmartObject;
use Users\User;

class AccountFormFactory
{
    use SmartObject;


    public function create(bool $isAjaxed = true): Form
    {
        $form = new Form;
        if ($isAjaxed === true) {
            $form->getElementPrototype()->class = 'ajax';
        }

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


        return $form;
    }
}