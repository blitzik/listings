<?php declare(strict_types=1);

namespace Common\Components;

class MetaTitleControl extends BaseControl
{
    /** @var string */
    private $pageTitle;


    /**
     * @param $pageTitle
     * @return MetaTitleControl
     */
    public function setTitle($pageTitle): MetaTitleControl
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/title.latte');

        $this->template->title = $this->pageTitle;

        $template->render();
    }
}


interface IMetaTitleControlFactory
{
    /**
     * @return MetaTitleControl
     */
    public function create(): MetaTitleControl;
}