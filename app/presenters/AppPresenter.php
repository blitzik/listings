<?php

namespace App\Presenters;

use App\Components\IFlashMessagesControlFactory;
use App\Components\IPageTitleControlFactory;
use App\Components\IMetaTagsControlFactory;
use Users\Authorization\Authorizator;
use Nette\Application\UI\Presenter;

abstract class AppPresenter extends Presenter
{
    /**
     * @var IFlashMessagesControlFactory
     * @inject
     */
    public $flashMessagesControlFactory;

    /**
     * @var IPageTitleControlFactory
     * @inject
     */
    public $pageTitleControlFactory;

    /**
     * @var IMetaTagsControlFactory
     * @inject
     */
    public $metaTagsControlFactory;

    /**
     * @var Authorizator
     * @inject
     */
    public $authorizator;


    protected function createComponentFlashMessages()
    {
        return $this->flashMessagesControlFactory
                    ->create();
    }


    protected function createComponentMetaTags()
    {
        return $this->metaTagsControlFactory
                    ->create();
    }


    protected function createComponentPageTitle()
    {
        return $this->pageTitleControlFactory
                    ->create('Active Sport Club');
    }


    public function refresh($redirect, array $snippets = null)
    {
        if ($this->isAjax()) {
            if (empty($snippets)) {
                $this->redrawControl();
            } else {
                foreach ($snippets as $snippet) {
                    $this->redrawControl($snippet);
                }
            }
        }

        if (!$this->isAjax()) {
            $this->redirect($redirect);
        }
    }
}