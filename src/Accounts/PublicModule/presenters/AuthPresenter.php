<?php declare(strict_types=1);

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\LoginControl;
use Common\AuthModule\Presenters\PublicPresenter;
use Accounts\Components\ILoginControlFactory;

final class AuthPresenter extends PublicPresenter
{
    /**
     * @var ILoginControlFactory
     * @inject
     */
    public $loginControlFactory;


    public function actionLogin(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Přihlášení');
    }


    public function renderLogin(): void
    {
    }


    protected function createComponentLogin(): LoginControl
    {
        $comp = $this->loginControlFactory->create();

        $comp->onSuccessfulLogin[] = function () {
            $this->redirect(':Listings:Member:Dashboard:default');
        };

        return $comp;
    }


    /*
     * --------------------
     * ----- LOGOUT -------
     * --------------------
     */


    public function actionLogOut(): void
    {
        $this->user->logout(true);
        $this->redirect(':Accounts:Public:Auth:logIn');
    }

}