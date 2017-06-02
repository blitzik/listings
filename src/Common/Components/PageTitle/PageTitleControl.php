<?php declare(strict_types=1);

namespace Common\Components;

class PageTitleControl extends BaseControl
{
    /** @var string */
    private $pageTitle;

    /** @var string */
    private $joinedText;


    /**
     * @param $pageTitle
     * @return PageTitleControl
     */
    public function setPageTitle($pageTitle): PageTitleControl
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }


    /**
     * @param string $joinedText
     */
    public function setJoinedText($joinedText): void
    {
        $this->joinedText = $joinedText;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/title.latte');

        $this->template->title = $this->pageTitle;
        $this->template->joinedText = $this->joinedText;

        $template->render();
    }
}


interface IPageTitleControlFactory
{
    /**
     * @return PageTitleControl
     */
    public function create(): PageTitleControl;
}