<?php declare(strict_types = 1);

namespace Listings\Components;

use Common\Components\FlashMessages\FlashMessage;
use Accounts\Facades\AccountFacade;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Users\User;

class UserDetailControl extends BaseControl
{
    public $onSuccessfulUpdate;


    /** @var AccountFacade */
    private $accountFacade;


    /** @var User */
    private $userEntity;


    public function __construct(
        User $userEntity,
        AccountFacade $accountFacade
    ) {
        $this->accountFacade = $accountFacade;
        $this->userEntity = $userEntity;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/userDetail.latte');


        $template->render();
    }


    protected function createComponentUserDetailForm(): Form
    {
        $form = new Form;
        $form->getElementPrototype()->class = 'ajax';

        $form->addText('first_name', 'Jméno', null, User::LENGTH_FIRSTNAME)
                ->setRequired('Zadejte Vaše křestní jméno')
                ->setDefaultValue($this->user->getIdentity()->getFirstName())
                ->addRule(Form::MAX_LENGTH, 'Křestní jméno může obsahovat max. %d znaků', User::LENGTH_FIRSTNAME);

        $form->addText('last_name', 'Příjmení', null, User::LENGTH_LASTNAME)
                ->setRequired('Zadejte Vaše příjmení')
                ->setDefaultValue($this->user->getIdentity()->getLastName())
                ->addRule(Form::MAX_LENGTH, 'Příjmení může obsahovat max. %d znaků', User::LENGTH_LASTNAME);

        $form->addSubmit('save', 'Uložit');


        $form->onSuccess[] = [$this, 'processUserData'];


        return $form;
    }


    public function processUserData(Form $form, $values): void
    {
        try {
            $this->accountFacade->updateAccount((array)$values, $this->userEntity);

            $this->flashMessage('Změny byly úspěšně uloženy.', FlashMessage::SUCCESS);

            $this->onSuccessfulUpdate();

        } catch (\Exception $e) {
            $this->flashMessage('Akci nelze dokončit. Zkuste to později.', FlashMessage::ERROR);
        }

        $this->refresh('this', ['flashMessages']);
    }
}


interface IUserDetailControlFactory
{
    /**
     * @param User $userEntity
     * @return UserDetailControl
     */
    public function create(User $userEntity): UserDetailControl;
}