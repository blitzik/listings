<?php declare(strict_types=1);

namespace Common\Presenters;

use blitzik\Authorization\Authorizator\Authorizator;
use Common\Components\IFlashMessagesControlFactory;
use Common\Components\IMetaTitleControlFactory;
use Common\Components\IPageTitleControlFactory;
use Common\Components\IMetaTagsControlFactory;
use Common\Components\FlashMessagesControl;
use Common\Components\MetaTitleControl;
use Common\Components\PageTitleControl;
use Common\Components\MetaTagsControl;
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

        $this->template->assetsVersion = '003';
    }


    protected function createComponentFlashMessages(): FlashMessagesControl
    {
        return $this->flashMessagesControlFactory
                    ->create();
    }


    protected function createComponentMetaTags(): MetaTagsControl
    {
        return $this->metaTagsControlFactory
                    ->create();
    }


    protected function createComponentMetaTitle(): MetaTitleControl
    {
        return $this->metaTitleControlFactory->create();
    }


    protected function createComponentPageTitle(): PageTitleControl
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