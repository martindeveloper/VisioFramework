<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Translate;

use Visio;

/**
 * Translate service
 *
 * @package Visio\Session
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Service extends Visio\Object implements Visio\DependencyInjection\IService {

    /**
     * @var IDriver
     */
    private $driver;

    /**
     * @param IDriver $driver
     */
    public function __construct(Visio\Translate\IDriver $driver) {
        $this->driver = $driver;
    }

    /**
     * @param \Visio\DependencyInjection\IContainer $container
     * @return \Visio\Translate
     */
    public function __invoke(Visio\DependencyInjection\IContainer $container) {
        $translationLang = $container->applicationConfig->get('language', 'Translate');

        $translate = new Visio\Translate($translationLang, $this->driver);
        $translate->container = $container;

        return $translate;
    }
}
