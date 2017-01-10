<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Mail\Transport;

use Jitamin\Foundation\Base;
use Jitamin\Foundation\Mail\ClientInterface;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_TransportException;

/**
 * PHP Mail Handler.
 */
class Mail extends Base implements ClientInterface
{
    /**
     * Send a HTML email.
     *
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param string $html
     * @param string $author
     */
    public function sendEmail($email, $name, $subject, $html, $author)
    {
        try {
            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom([$this->helper->mail->getMailSenderAddress() => $author])
                ->setTo([$email => $name])
                ->setBody($html, 'text/html');

            Swift_Mailer::newInstance($this->getTransport())->send($message);
        } catch (Swift_TransportException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Get SwiftMailer transport.
     *
     * @return \Swift_Transport|\Swift_MailTransport|\Swift_SmtpTransport|\Swift_SendmailTransport
     */
    protected function getTransport()
    {
        return Swift_MailTransport::newInstance();
    }
}
