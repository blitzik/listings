<?php

namespace Listings\Presenters;

use App\MemberModule\Presenters\SecuredPresenter;

final class DashboardPresenter extends SecuredPresenter
{
    public function actionDefault()
    {
        $this['pageTitle']->setPageTitle('Test');
    }


    public function renderDefault()
    {
    }
}