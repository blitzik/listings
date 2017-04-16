<?php declare(strict_types=1);

namespace Accounts\Services;

use Nette\Application\UI\ITemplateFactory;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplate;
use Nette\SmartObject;

class ForgottenEmailTemplateFactory
{
    use SmartObject;


    /** @var ITemplateFactory */
    private $templateFactory;

    /** @var LinkGenerator */
    private $linkGenerator;


    public function __construct(
        LinkGenerator $linkGenerator,
        ITemplateFactory $templateFactory
    ) {
        $this->linkGenerator = $linkGenerator;
        $this->templateFactory = $templateFactory;
    }


    /**
     * @param string $recipientEmail
     * @param string $applicationUrl
     * @param string $adminFullName
     * @param string $token
     * @param string|null $templatePath
     * @return ITemplate
     */
    public function create(
        string $recipientEmail,
        string $applicationUrl,
        string $adminFullName,
        string $token,
        string $templatePath = null
    ): ITemplate {
        $template = $this->templateFactory->createTemplate();
        if ($templatePath === null) {
            $template->setFile(__DIR__ . '/email.latte');
        } else {
            $template->setFile($templatePath);
        }

        $template->linkGenerator = $this->linkGenerator;

        $template->applicationUrl = $applicationUrl;
        $template->adminFullName = $adminFullName;

        $template->email = $recipientEmail;
        $template->token = $token;

        return $template;
    }
}