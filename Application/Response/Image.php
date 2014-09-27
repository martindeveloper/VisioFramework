<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application\Response;

use Visio;

/**
 * Response class for images.
 *
 * @package Visio\Application\Response
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Image extends Visio\Object implements Visio\Application\IResponse {

    /**
     * @var Visio\FileSystem\File $file
     */
    public $file;

    /**
     * __construct()
     * 
     * @param Visio\FileSystem\File $file
     */
    public function __construct(Visio\FileSystem\File $file) {
        $this->file = $file;
    }

    /**
     * @param \Visio\Http\Response $httpResponse
     * @param \Visio\Http\Request $httpRequest
     * @param \Visio\DependencyInjection\IContainer $container
     * @return mixed|void
     * @throws \Visio\Exception\Response
     */
    public function send(Visio\Http\Response $httpResponse, Visio\Http\Request $httpRequest, Visio\DependencyInjection\IContainer $container) {
        try {
            $httpResponse->setContentType($this->file->mimeType);
            $respond = $this->file->content;
        } catch (Visio\Exception\File $ex) {
            throw new Visio\Exception\Response("Can not send image to client '" . $ex->getMessage() . "'!");
        }

        Visio\Events::dispatch("ResponseImage-onSend", array(&$this, &$respond));

        echo $respond;
    }

}
