<?php declare(strict_types=1);

namespace Common\Components;

class MetaTagsControl extends BaseControl
{
    /** @var array */
    private $metas = [];


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/metas.latte');

        $template->metas = $this->metas;

        $template->render();
    }


    public function addMeta($name, $content): void
    {
        if (!empty($content)) {
            $this->metas[$name] = $content;
        }
    }
}


interface IMetaTagsControlFactory
{
    /**
     * @return MetaTagsControl
     */
    public function create(): MetaTagsControl;
}