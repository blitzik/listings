<?php

declare(strict_types = 1);

namespace Accounts\Services;

use Accounts\Exceptions\Runtime\EmailSendingFailedException;
use blitzik\email\MailMessageFactory;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Monolog\Logger;
use Nette\Mail\IMailer;
use Nette\SmartObject;
use Users\User;

class ForgottenPasswordEmailSender
{
    use SmartObject;


    /** @var ForgottenEmailTemplateFactory */
    private $forgottenEmailTemplateFactory;

    /** @var MailMessageFactory */
    private $mailMessageFactory;

    /** @var IMailer */
    private $mailer;

    /** @var Logger */
    private $logger;

    /** @var EntityManager */
    private $em;


    public function __construct(
        Logger $logger,
        IMailer $mailer,
        EntityManager $entityManager,
        MailMessageFactory $mailMessageFactory,
        ForgottenEmailTemplateFactory $forgottenEmailTemplateFactory
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger->channel('forgotten-password');
        $this->em = $entityManager;
        $this->mailMessageFactory = $mailMessageFactory;
        $this->forgottenEmailTemplateFactory = $forgottenEmailTemplateFactory;
    }


    /**
     * @param User $user
     * @param string $senderEmail
     * @param string $applicationUrl
     * @param string $adminFullName
     * @throws EmailSendingFailedException
     */
    public function send(
        User $user,
        string $senderEmail,
        string $applicationUrl,
        string $adminFullName
    ) {
        $this->logger->addInfo(sprintf('Try: %s', $user->getEmail()));

        $token = $user->createToken((new \DateTime())->modify('+ 1 day'));
        try {
            $this->em->flush();

            $mail = $this->prepareEmail(
                $user->getEmail(),
                $senderEmail,
                $applicationUrl,
                $adminFullName,
                $token
            );

            $this->mailer->send($mail);

        } catch (\Exception $e) {
            $this->logger->addCritical(sprintf('Failure: %s; Error: code %s; message: %s', $user->getEmail(), $e->getCode(), $e->getMessage()));
            $this->em->rollback();
            $this->em->close();

            throw new EmailSendingFailedException;
        }
    }


    private function prepareEmail(
        string $recipientEmail,
        string $senderEmail,
        string $applicationUrl,
        string $adminFullName,
        string $token
    ) {
        $mailContent = $this->forgottenEmailTemplateFactory
                            ->create(
                                $recipientEmail,
                                $applicationUrl,
                                $adminFullName,
                                $token
                            );

        $mail = $this->mailMessageFactory
                     ->create(
                         $recipientEmail,
                         $senderEmail,
                         'Obnova zapomenutého hesla',
                         $mailContent
                     );

        return $mail;
    }
}