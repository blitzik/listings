<?php declare(strict_types=1);

namespace Common\Components;

class PageTitleControl extends BaseControl
{
    /** @var string */
    private $pageTitle;

    /** @var string */
    private $joinedText;

    /** @var string */
    private $url;


    public function setPageTitle($pageTitle): PageTitleControl
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }


    public function makeItLink(string $url)
    {
        $this->url = $url;
    }


    public function setJoinedText($joinedText): void
    {
        $this->joinedText = $joinedText;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/title.latte');

        $template->title = $this->pageTitle;
        $template->joinedText = $this->joinedText;
        $template->url = $this->url;

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