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
 * Main application class.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Application extends Visio\Object implements Visio\Application\IDefault {

    const ENV_DEBUG = "development";
    const ENV_PROD = "productive";

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    /**
     * @var Visio\Http\Request $httpRequest
     */
    public $httpRequest;

    /**
     * @var Visio\Http\Response $httpResponse
     */
    public $httpResponse;

    /**
     * @var Visio\Diagnostic\Logger $log
     */
    public $log;

    /**
     * @var Visio\Router $router
     */
    public $router;

    /**
     * @var Visio\Config $config
     */
    protected $config;

    /**
     * @var bool $bufferStarted
     */
    protected $bufferStarted = false;

    /**
     * Application current environment
     * @var string
     */
    public $environment = self::ENV_DEBUG;

    /**
     * __construct()
     *
     * @param Visio\DependencyInjection\IContainer $container
     */
    public function __construct(Visio\DependencyInjection\IContainer $container) {
        $this->container = $container;

        $this->log = $this->container->logger;
        $this->router = $this->container->router;

        $this->config = $this->container->applicationConfig;
        $this->environment = $this->config->get("environment", "Application");

        Visio\Loader::getInstance()->addPrefix("Application", APP_DIR);
    }

    /**
     * Run application
     *
     * @return bool
     * @throws Exception\Application
     */
    public function run() {
        if ($this->config->get('startSessionAutomatic', 'Application') == 'true') {
            #start session
            $session = $this->container->session;
            Visio\Events::dispatch("Application-onSessionStart", array(&$this,
                                                                       &$session));
        }

        #try to start output buffering
        $this->startBuffer('ob_gzhandle');

        try {
            $this->httpRequest = new Visio\Http\Request(true);
            $this->httpResponse = new Visio\Http\Response();

            #route
            try {
                $this->router->connect($this->httpRequest);
            } catch (Visio\Exception\Router $ex) {
                throw new Visio\Exception\Application($ex->getMessage());
            }
        } catch (Visio\Exception\Application $ex) {
            #show error page
            $ex->showErrorMessage();

            return false;
        }
    }

    /**
     * startBuffer()
     *
     * @param string $callback
     * @return bool
     */
    public function startBuffer($callback = "") {
        if ($this->config->get("outputBuffer", "Application") != true || $this->config->get("outputBuffer", "Application") != "1") {
            return false;
        }

        if ($this->bufferStarted === true) {
            //throw new Visio\Exception\Application("Output buffer already started!");
            return false;
        }

        if (function_exists($callback) && ob_start($callback)) {
            chdir(dirname($_SERVER['SCRIPT_FILENAME']));
            $this->bufferStarted = true;
            return true;
        }

        //throw new Visio\Exception\Application("Can not start output buffer!");
        return false;
    }

    /**
     * Set new config
     *
     * @param Config $config
     */
    public function setConfig(Visio\Config $config) {
        $this->config = $config;
    }

    /**
     * setRoute()
     *
     * @param string $mask
     * @param mixed $metadata
     */
    public function setRoute($mask, $metadata = array()) {
        # Gets flags from args
        $flags = array();
        $args = func_get_args();
        $argsSize = sizeof($args);
        if ($argsSize > 2) {
            for ($a = 2; $a < $argsSize; $a++) {
                $flags[] = $args[$a];
            }
        }

        $router = $this->router;
        $router[] = new Visio\Route($mask, $metadata, $flags);
    }

    /**
     * Set routes from file
     *
     * @param FileSystem\File $file
     */
    public function setRoutesFromFile(Visio\FileSystem\File $file) {
        $parser = new Visio\Router\FileParser($file);
        $parsedRoutes = $parser->parse();

        $router = $this->router;

        foreach ($parsedRoutes as $route) {
            $router[] = $route;
        }
    }

    /**
     * getRouter()
     *
     * @return Visio\Router
     */
    public function getRouter() {
        return $this->router;
    }

}
