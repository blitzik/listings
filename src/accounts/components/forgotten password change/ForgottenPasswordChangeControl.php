<?php

namespace Accounts\Components;

use Accounts\Facades\AccountFacade;
use Accounts\Services\Factories\AccountFormFactory;
use App\Components\BaseControl;
use Nette\Application\UI\Form;
use Users\User;

class ForgottenPasswordChangeControl extends BaseControl
{
    public $onSuccessfulPasswordChange;


    /** @var AccountFormFactory */
    private $accountFormFactory;

    /** @var AccountFacade */
    private $accountFacade;


    /** @var User */
    private $account;


    public function __construct(
        User $user,
        AccountFacade $accountFacade,
        AccountFormFactory $accountFormFactory
    ) {
        $this->account = $user;
        $this->accountFormFactory = $accountFormFactory;
        $this->accountFacade = $accountFacade;
    }


    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/forgottenPasswordChange.latte');



        $template->render();
    }


    protected function createComponentForm()
    {
        $form = $this->accountFormFactory->create(false);
        $form['email']->setOmitted()
                      ->setAttribute('readOnly', 'readOnly')
                      ->setDefaultValue($this->account->getEmail());

        $form['firstName']->setOmitted()
                          ->setAttribute('readOnly', 'readOnly')
                          ->setDefaultValue($this->account->getFirstName());

        $form['lastName']->setOmitted()
                         ->setAttribute('readOnly', 'readOnly')
                         ->setDefaultValue($this->account->getLastName());

        $form['save']->caption = 'uložit';


        $form->onSuccess[] = [$this, 'processForm'];


        return $form;
    }


    public function processForm(Form $form, $values)
    {
        $this->accountFacade->changePassword($this->account, $values['pass']);

        $this->onSuccessfulPasswordChange();
    }
}


interface IForgottenPasswordChangeControlFactory
{
    /**
     * @param User $user
     * @return ForgottenPasswordChangeControl
     */
    public function create(User $user);
}