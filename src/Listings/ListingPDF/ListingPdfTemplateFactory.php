<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\ITemplate;
use Nette\SmartObject;

class ListingPdfTemplateFactory
{
    use SmartObject;


    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_SEP = 'sep';

    const ITEM_DEFAULT = 'default';


    /** @var ITemplateFactory */
    private $templateFactory;


    public function __construct(
        ITemplateFactory $templateFactory
    ) {
        $this->templateFactory = $templateFactory;
    }


    /**
     * @param string $layoutAppearance
     * @param string $itemAppearance
     * @param ListingPdfDTO[] $listingPdfDTOs
     * @return ITemplate
     */
    public function create(string $layoutAppearance, string $itemAppearance, array $listingPdfDTOs): ITemplate
    {
        foreach ($listingPdfDTOs as $listingPdfDTO) {
            if (!$listingPdfDTO instanceof ListingPdfDTO) {
                throw new InvalidArgumentException;
            }
        }

        $template = $this->templateFactory->createTemplate();
        $template->setFile(sprintf('%s/templates/layouts/%s/layout.latte', __DIR__, $layoutAppearance));

        $template->listings = $listingPdfDTOs;

        $template->itemAppearance = $itemAppearance;


        return $template;
    }
}