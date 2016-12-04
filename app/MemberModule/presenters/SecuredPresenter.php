<?php

namespace App\MemberModule\Presenters;

use App\Presenters\AppPresenter;
use Listings\Services\TimeUtils;

abstract class SecuredPresenter extends AppPresenter
{
    /** @var string */
    protected $chosenYear;

    /** @var string */
    protected $_backLink;


    protected function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->_backLink = $this->storeRequest();
            $this->redirect(':Accounts:Auth:login');
        }
    }


    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->_userEntity = $this->user->getIdentity();
        $this->template->_years = TimeUtils::generateYearsForSelection();
        $this->template->_chosenYear = $this->chosenYear;
    }


    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }

        return __DIR__ . '/templates/@layout.latte';
    }
}