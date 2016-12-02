<?php

namespace App\AuthModule\Presenters;

use App\Presenters\AppPresenter;

abstract class PublicPresenter extends AppPresenter
{
    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }

        return __DIR__ . '/templates/@layout.latte';
    }

}