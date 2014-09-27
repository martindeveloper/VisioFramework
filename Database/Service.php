<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Database;

use Visio;

/**
 * Database service
 *
 * @package Visio\Database
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Service extends Visio\Object implements Visio\DependencyInjection\IService {

    /**
     *
     * @var Visio\Database\IHandler
     */
    private $handler;

    /**
     * @var array
     */
    private $credentials;

    /**
     * __construct
     *
     * @param Visio\Database\IHandler $handler
     * @param array $credentials
     */
    public function __construct(Visio\Database\IHandler $handler, array $credentials) {
        $this->handler = $handler;
        $this->credentials = $credentials;
    }

    /**
     * @param \Visio\DependencyInjection\IContainer $container
     * @return mixed
     */
    public function __invoke(Visio\DependencyInjection\IContainer $container) {
        try {
            $this->handler->connect($this->credentials);
        } catch (Visio\Exception\Database $ex) {
            $ex->showErrorMessage();
        }

        return $this->handler->dbo;
    }


}
