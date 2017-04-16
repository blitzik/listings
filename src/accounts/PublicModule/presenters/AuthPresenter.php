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


    public function actionLogin()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect(':Listings:Member:Dashboard:default');
        }

        $this['metaTitle']->setTitle('Přihlášení');
    }


    public function renderLogin()
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


    public function actionLogOut()
    {
        $this->user->logout(true);
        $this->redirect(':Accounts:Public:Auth:logIn');
    }

}