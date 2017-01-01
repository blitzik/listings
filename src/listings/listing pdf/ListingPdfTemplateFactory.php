<?php

declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\ITemplate;
use Nette\SmartObject;
use Latte\Engine;

class ListingPdfTemplateFactory
{
    use SmartObject;


    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_SEP = 'sep';

    const ITEM_DEFAULT = 'default';


    /** @var ITemplateFactory */
    private $templateFactory;

    /** @var Engine */
    private $latteEngine;


    public function __construct(
        ILatteFactory $latteFactory,
        ITemplateFactory $templateFactory
    ) {
        $this->latteEngine = $latteFactory->create();
        $this->templateFactory = $templateFactory;
    }


    /**
     * @param string $layoutType
     * @param string $itemType
     * @param ListingPdfDTO[] $listingPdfDTOs
     * @return ITemplate
     */
    public function create(string $layoutType, string $itemType, array $listingPdfDTOs): ITemplate
    {
        foreach ($listingPdfDTOs as $listingPdfDTO) {
            if (!$listingPdfDTO instanceof ListingPdfDTO) {
                throw new InvalidArgumentException;
            }
        }

        $template = $this->templateFactory->createTemplate();
        $template->setFile(sprintf('%s/templates/layouts/%s.latte', __DIR__, $layoutType));

        $template->listings = $listingPdfDTOs;
        $template->itemType = $itemType;


        return $template;
    }
}