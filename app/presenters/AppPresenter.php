<?php

namespace App\Presenters;

use App\Components\IFlashMessagesControlFactory;
use App\Components\IMetaTitleControlFactory;
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
     * @var IMetaTitleControlFactory
     * @inject
     */
    public $metaTitleControlFactory;

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


    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->assetsVersion = '001';
    }


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


    protected function createComponentMetaTitle()
    {
        return $this->metaTitleControlFactory->create();
    }


    protected function createComponentPageTitle()
    {
        return $this->pageTitleControlFactory->create();
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