<?php

namespace App\MemberModule\Presenters;

use App\Presenters\AppPresenter;

abstract class SecuredPresenter extends AppPresenter
{
    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }

        return __DIR__ . '/templates/@layout.latte';
    }
}