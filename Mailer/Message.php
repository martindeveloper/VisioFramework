<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Mailer;

use Visio;

/**
 * Visio\Mailer\Message
 * 
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Message extends Visio\Object implements Visio\Mailer\IMessage {

    private $subject;
    private $body;
    //private $attachments = array();
    private $contentType = "text/plain";
    protected $html = false;
    protected $charset = "utf-8";

    /**
     * __construct()
     * 
     * @param string $subject
     * @param string $body
     */
    public function __construct($subject, $body) {
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * setCharset()
     * 
     * @param string $charset
     */
    public function setCharset($charset) {
        $this->charset = $charset;
    }

    /**
     * setHtml()
     * 
     * @param bool $bool
     */
    public function setHtml($bool) {
        if ($bool === true) {
            $this->html = $bool;
            $this->contentType = "text/html";
        } else {
            $this->html = false;
            $this->contentType = "text/plain";
        }
    }

    /**
     * setSubject()
     * 
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * setBody()
     * 
     * @param string $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * setAttachment()
     * 
     * @param Visio\Mailer\Attachment $attachment
     */
    public function setAttachment(/* Visio\Mailer\Attachment $attachment */) {
        /*
          if (!$attachment instanceof Visio\Mailer\Attachment) {
          throw new Visio_Exception("Visio\Mailer attachment must be Visio\Mailer\Attachment instance!");
          }
          $this->attachments[] = $attachment;

          return $this;
         */
        throw new Visio\Exception\General("Not implemented yet!");
    }

    /**
     * __get()
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            throw new Visio\Exception\MemberAccess("Cannot read an undeclared property " . $this->getClassName() . "::\$" . $name . "!");
        }
    }

}