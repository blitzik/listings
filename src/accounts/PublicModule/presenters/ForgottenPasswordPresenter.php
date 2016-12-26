<?php

namespace Accounts\PublicModule\Presenters;

use Accounts\Components\IForgottenPasswordFormControlFactory;
use App\AuthModule\Presenters\PublicPresenter;

final class ForgottenPasswordPresenter extends PublicPresenter 
{
    /**
     * @var IForgottenPasswordFormControlFactory
     * @inject
     */
    public $forgottenPasswordFormControlFactory;


    public function actionRequest()
    {
    }
    
    
    public function renderRequest()
    {
        
    }


    protected function createComponentRequestForm()
    {
        $comp = $this->forgottenPasswordFormControlFactory->create();

        return $comp;
    }


    /*
     * --------------------
     * ----- CHANGE -------
     * --------------------
     */


    public function actionChange($email, $token)
    {

    }


    public function renderChange($email, $token)
    {

    }

}