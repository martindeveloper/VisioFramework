<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Mailer class for sending emails to multiple recipients.
 * Support attachments, multiple recipients.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Mailer extends Visio\Object {

    /**
     * @var Visio\Mailer\IHandler $handler
     */
    protected $handler;

    /**
     * @var array $recipients
     */
    private $recipients = array();

    /**
     * @var Visio\Mailer\Address $from
     */
    private $from;

    /**
     * @var Visio\Mailer\Message $message
     */
    private $message;

    /**
     * @var Visio\Config $config
     */
    private $config;

    /**
     * __construct()
     *
     * @param Visio\Config $config
     */
    public function __construct(Visio\Config $config) {
        $this->config = $config;
    }

    /**
     * Set new handler for sending
     *
     * @param Visio\Mailer\IHandler $handler
     * @return Visio\Mailer
     */
    public function setHandler(Visio\Mailer\IHandler $handler) {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Set from field
     *
     * @param Visio\Mailer\Address $address
     * @return Visio\Mailer
     */
    public function setFrom(Visio\Mailer\Address $address) {
        $this->from = $address;

        return $this;
    }

    /**
     * Add a new recipient
     *
     * @param Visio\Mailer\Address $address
     * @return Visio\Mailer
     */
    public function setRecipient(Visio\Mailer\Address $address) {
        $this->recipients[] = $address;

        return $this;
    }

    /**
     * Clear all recipients
     *
     * @return Visio\Mailer
     */
    public function clearRecipients() {
        $this->recipients = array();

        return $this;
    }

    /**
     * Set a new message
     *
     * @param Visio\Mailer\Message $message
     * @return Visio\Mailer
     */
    public function setMessage(Visio\Mailer\Message $message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Send message
     */
    public function send() {
        $this->handler->prepare($this->recipients, $this->from, $this->message);

        return $this->handler->send();
    }

}