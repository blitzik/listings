<?php declare(strict_types=1);

namespace blitzik\email;

use Nette\Application\UI\ITemplate;
use Nette\Mail\Message;

class MailMessageFactory
{
    /**
     * @param string|array $recipients
     * @param string $sender
     * @param string $subject
     * @param ITemplate $content
     * @return Message
     */
    public function create($recipients, $sender, $subject, ITemplate $content): Message
    {
        $mail = new Message();
        $mail->setFrom($sender)
             ->setSubject($subject)
             ->setHtmlBody($content);

        if (is_string($recipients)) {
            $recipients = [$recipients];
        } else {
            if (!is_array($recipients)) {
                throw new \InvalidArgumentException('Only string or array of recipient emails can pass');
            }
        }

        foreach ($recipients as $recipient) {
            $mail->addTo($recipient);
        }

        return $mail;
    }
}