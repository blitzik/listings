<?php declare(strict_types=1);

namespace Common\AuthModule\Presenters;

use Common\Presenters\AppPresenter;

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