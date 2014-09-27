<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Mailer\Handler;

use Visio;

/**
 * Default PHP mail handler for Visio\Mailer
 *
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class PhpMail extends Visio\Object implements Visio\Mailer\IHandler {

    /**
     * @var Visio\Mailer\Message $message
     */
    private $message;

    /**
     * @var Visio\Mailer\Address $from
     */
    private $from;

    /**
     * @var array $recipients
     */
    private $recipients;

    /**
     * __construct()
     */
    public function __construct() {

    }

    /**
     * prepare()
     *
     * @param array $recipients
     * @param Visio\Mailer\Address $from
     * @param Visio\Mailer\Message $message
     */
    public function prepare($recipients, $from, $message) {
        $this->message = $message;
        $this->from = $from;
        $this->recipients = $recipients;
    }

    /**
     * Send mails
     *
     * @return bool
     */
    public function send() {
        $checksum = sizeof($this->recipients);
        //$token = Visio\Utilities::createToken();

        $iteration = 1;

        foreach ($this->recipients as $recipient) {
            $headers = $this->buildHeaders($recipient);

            if (Visio\Utilities\String::lower($this->message->charset) == 'utf-8') {
                $subject = "=?utf-8?B?" . base64_encode(Visio\Utilities::toUTF8($this->message->subject)) . "?=";
                $headers .= "Content-Transfer-Encoding: base64\r\n \r\n";
                $text = base64_encode(Visio\Utilities::toUTF8($this->message->body));
            } else {
                $subject = "" . $this->message->subject . "";
                $headers .= "Content-Transfer-Encoding: 7bit\r\n";
                $text = $this->message->body;
            }

            //TODO: Attachment support

            if (mail("", $subject, $text, $headers)) {
                $iteration++;
            }
        }

        if (!$iteration == $checksum) {
            throw new Visio\Exception\Mailer("Can not send message to all recipients!");
        }
    }

    /**
     * Build headers
     *
     * @param Visio\Mailer\Address $recipient
     * @return string
     */
    public function buildHeaders(Visio\Mailer\Address $recipient) {
        $fromName = $this->from->getName();
        $recName = $recipient->getName();

        $headers = "";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: " . $this->message->contentType . "; charset=" . Visio\Utilities\String::lower($this->message->charset) . "\r\n";
        $headers .= "From: \"" . (empty($fromName) ? $this->from->getAddress() : $fromName) . "\" <" . $this->from->getAddress() . "> \r\n";
        $headers .= "To: \"" . (empty($recName) ? $recipient->getAddress() : $recName) . "\" <" . $recipient->getAddress() . "> \r\n";

        return $headers;
    }

}
