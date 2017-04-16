<?php declare(strict_types=1);

namespace DatabaseBackup\Subscribers;

use DatabaseBackup\Services\DatabaseBackup;
use DatabaseBackup\Services\BackupResult;
use DatabaseBackup\Services\BackupFile;
use Nette\Mail\SendException;
use Kdyby\Events\Subscriber;
use Kdyby\Monolog\Logger;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\SmartObject;

class DatabaseBackupSubscriber implements Subscriber
{
    use SmartObject;


    /** @var IMailer */
    private $mailer;

    /** @var Logger */
    private $logger;


    /** @var array */
    private $infoReceivers;

    /** @var array */
    private $fileReceivers;

    /** @var string */
    private $sender;


    public function __construct(
        string $sender,
        array $receivers,
        IMailer $mailer,
        Logger $logger
    ) {
        $this->sender = $sender;
        $this->infoReceivers = array_flip($receivers['info']);
        $this->fileReceivers = array_flip($receivers['file']);

        $this->mailer = $mailer;
        $this->logger = $logger->channel('database-backup');
    }


    function getSubscribedEvents()
    {
        return [
            DatabaseBackup::class . '::onSuccessfulDatabaseBackup',
            DatabaseBackup::class . '::onDatabaseBackupFailure',

        ];
    }


    public function onSuccessfulDatabaseBackup(BackupFile $backupFile, BackupResult $backupResult)
    {
        $messageContent = '<p>Database backup success</p>';
        if ($backupResult->hasErrors()) {
            $messageContent .= '<p>Errors:</p>';
            $messageContent .= '<ul>';
            foreach ($backupResult->getErrors() as $error) {
                $messageContent .= sprintf('<li>%s</li>', $error->getMessage());
            }
            $messageContent .= '</ul>';
        }

        foreach ($this->infoReceivers as $receiver => $nothing) {
            try {
                $mail = new Message();
                $mail->setFrom($this->sender)
                    ->addTo($receiver)
                    ->setHtmlBody($messageContent);
                if (array_key_exists($receiver, $this->fileReceivers)) {
                    $mail->addAttachment($backupFile->getFilePath());
                }

                $this->mailer->send($mail);

            } catch (SendException $e) {
                $this->logger->addCritical(sprintf('Info-mail sending\'s failed. [%s]', $receiver));
            }
        }
    }


    public function onDatabaseBackupFailure()
    {
        foreach ($this->infoReceivers as $receiver => $nothing) {
            try {
                $mail = new Message();
                $mail->setFrom($this->sender)
                     ->addTo($receiver)
                     ->setBody('Database backup\'s failed');

                $this->mailer->send($mail);

            } catch (SendException $e) {
                $this->logger->addCritical(sprintf('Info-mail sending\'s failed. [%s]', $receiver));
            }
        }
    }
}