<?php

namespace App\AuthModule\Presenters;

use App\Presenters\AppPresenter;

abstract class AuthPresenter extends AppPresenter
{
    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }

        return __DIR__ . '/templates/@layout.latte';
    }

}