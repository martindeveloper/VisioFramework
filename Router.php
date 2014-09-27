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
 * Router for routing by url query index ?query=
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Router extends Visio\ArrayList implements Visio\Router\IRouter {

    /**
     * Array of default setting for routing.
     * @var array
     */
    public static $defaults;

    /**
     * Error controller name.
     * @var array
     */
    public static $errorController;

    /**
     * The controller path.
     * @var string
     */
    public $controllerPath;

    /**
     * The class prefix.
     * @var string
     */
    public $controllerPrefix;

    /**
     * The method prefix
     * @var string
     */
    public $methodPrefix;

    /**
     * Status of controller
     * @var bool
     */
    public $isLoaded = false;

    /**
     * @var array
     */
    public $metadata;

    /**
     * @var array
     */
    public $flags;

    /**
     * @var Visio\Http\Request $httpRequest
     */
    private $httpRequest;

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    private $container;

    /**
     * __construct()
     *
     */
    public function __construct(Visio\DependencyInjection\IContainer $container) {
        $this->controllerPrefix = $container->applicationConfig->get("controllerPrefix", "Router");
        $this->methodPrefix = $container->applicationConfig->get("methodPrefix", "Router");

        $loader = Visio\Loader::getInstance();
        $path = $loader->getPath($this->controllerPrefix);

        if ($path !== false) {
            $this->controllerPath = $path["path"];
        } else {
            $this->controllerPath = APP_DIR . "Controllers" . DS;
        }

        $this->container = $container;

        #Set default values for routing
        self::$defaults[self::LANG][self::K_DEFAULT] = self::DEFAULT_LANG;
        self::$defaults[self::CONTROLLER][self::K_DEFAULT] = "Index";
        self::$defaults[self::DOACTION][self::K_DEFAULT] = "index";

        self::$errorController[self::LANG][self::K_DEFAULT] = self::DEFAULT_LANG;
        self::$errorController[self::CONTROLLER][self::K_DEFAULT] = "Error";
        self::$errorController[self::DOACTION][self::K_DEFAULT] = "index";
    }

    /**
     * @param Http\Request $httpRequest
     * @return bool
     * @throws Exception\Router
     */
    public function connect(Visio\Http\Request $httpRequest) {
        $this->httpRequest = $httpRequest;

        if ($this->httpRequest->getQueryIndex('query') == null) {
            $this->load(self::$defaults);
        }

        if (count($this) > 0) {
            foreach ($this as $route) {
                if ($route instanceof Visio\Route) {
                    if ($metadata = $route->connect($httpRequest)) {
                        $this->load($metadata);

                        if ($this->isLoaded === true) {
                            break;
                        }
                    }
                } else {
                    throw new Visio\Exception\Router('$route is not instance of Visio\Route.');
                }
            }
        }

        # Try to load error controller
        if ($this->isLoaded === false) {
            $metadata = $route->connect($httpRequest);
            self::$errorController[self::LANG][self::K_VALUE] = $metadata[self::LANG][self::K_VALUE];
            if ($this->load(self::$errorController) === false) {
                throw new Visio\Exception\Router('Can not load error controller!');
            }
        }

        return true;
    }

    /**
     * @param $metadata
     * @return bool
     */
    protected function load($metadata) {
        $lang = !empty($metadata[self::LANG][self::K_VALUE]) ? $metadata[self::LANG][self::K_VALUE] : $metadata[self::LANG][self::K_DEFAULT];

        $controller = !empty($metadata[self::CONTROLLER][self::K_VALUE]) ? ucfirst($metadata[self::CONTROLLER][self::K_VALUE]) : ucfirst($metadata[self::CONTROLLER][self::K_DEFAULT]);

        $controllerFile = $this->controllerPath . $controller . '.php';

        if (!Visio\FileSystem::fileExist($controllerFile)) {
            return false;
            //throw new VisioException_Router('File \'' . $controllerFile . '\' doesn\'t exists!');
        } else {
            require $controllerFile;
        }

        $class = empty($this->controllerPrefix) ? $controller : $this->controllerPrefix . '\\' . $controller;
        $params[self::CONTROLLER] = $class;

        $action = !empty($metadata[self::DOACTION][self::K_VALUE]) ? ucfirst($metadata[self::DOACTION][self::K_VALUE]) : ucfirst($metadata[self::DOACTION][self::K_DEFAULT]);
        $action = str_replace("-", "_", $action);
        $params[self::ACTION] = $action;
        $action = $this->methodPrefix . $action;

        $locator = new Visio\Application\Controller\Locator($class, $action, $this->container);

        if (!$locator->actionExist($action)) {
            {
                return false;
            }
        }

        $params[self::DOACTION] = $action;

        if (!isset($metadata[self::NAMES]) || !is_array($metadata[self::NAMES])) {
            $metadata[self::NAMES] = array();
        }

        foreach ($metadata[self::NAMES] as $name) {
            if (isset($metadata[$name])) {
                $params[$name] = !empty($metadata[$name][self::K_VALUE]) ? $metadata[$name][self::K_VALUE] : $metadata[$name][self::K_DEFAULT];
            }
        }

        if (isset($metadata[self::ALLOW_ARGS])) {
            foreach ($metadata[self::ALLOW_ARGS] as $arg) {
                $params[] = $arg;
            }
        }

        $this->runObject($locator, $lang, $params);

        return true;
    }

    /**
     * Run controller object by controller locator
     *
     * @param Visio\Application\Controller\Locator $locator
     * @param string $lang
     * @param array $params
     * @return void
     */
    public function runObject(Visio\Application\Controller\Locator $locator, $lang, array $params = array()) {
        $dependency = array("lang" => $lang,
                            "args" => $params);
        $locator->execute($dependency);

        $this->isLoaded = true;
    }

    /**
     * Set defaults routing values
     *
     * @param array $defaults
     * @return void
     */
    public function setDefaults(array $defaults) {
        foreach ($defaults as $key => $def) {
            self::$defaults[$key][self::K_DEFAULT] = $def;
        }
    }

    /**
     * Set error controller
     *
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function setErrorController($controller, $action) {
        self::$errorController[self::LANG][self::K_DEFAULT] = self::DEFAULT_LANG;
        self::$errorController[self::CONTROLLER][self::K_DEFAULT] = $controller;
        self::$errorController[self::DOACTION][self::K_DEFAULT] = $action;
    }

    /**
     * Set new controllers path
     *
     * @param string $path
     * @throws Visio\Exception\DirectoryNotFound
     * @return void
     */
    public function setPath($path) {
        if (is_dir($path)) {
            $this->controllerPath = $path;
        } else {
            throw new Visio\Exception\DirectoryNotFound("Path '" . $path . "' is not valid!");
        }
    }

    /**
     * Set new controllers prefix
     *
     * @param string $prefix
     * @return void
     */
    public function setPrefix($prefix) {
        $this->controllerPrefix = $prefix;
    }

    /**
     * Create reverse link
     *
     * @param $controller
     * @param $action
     * @param array $args
     * @return string
     * @throws Exception
     */
    public function createReverseRoute($controller, $action, array $args = array()) {
        $controller = Visio\Utilities\String::ucfirst($controller);
        $linkMask = null;

        foreach ($this->container->router as $val) {
            $metadata = $val->metadata;

            if ($val->universal == true) {
                $linkMask = $val->mask;
                preg_match_all("~\<\:([A-Za-z]+)~u", $linkMask, $matches);
                preg_match_all("~\[\/*\<\:([A-Za-z]+)~u", $linkMask, $optional);
                $optional = $optional[1];

                $linkMask = "";

                foreach ($matches[1] as $identifier) {
                    switch ($identifier) {
                        case "lang":
                            $linkMask .= (isset($args["lang"]) ? Visio\Utilities\String::lower($args["lang"]) . "/" : "");
                            unset($args["lang"]);
                            break;

                        case "controller":
                            $linkMask .= Visio\Utilities\String::lower($controller) . "/";
                            break;


                        case "doAction":
                            $linkMask .= Visio\Utilities\String::lower($action) . "/";
                            break;

                        default:
                            if (isset($args[$identifier])) {
                                $linkMask .= Visio\Utilities\String::lower($args[$identifier]) . "/";
                            } else {
                                if (!in_array($identifier, $optional, true)) {
                                    throw new Visio\Exception("URL mask identifier '" . $identifier . "' value not specified!");
                                }
                            }
                            break;
                    }
                }

                if (isset($val->flags[0]) && $val->flags[0] == Visio\Route::ALLOW_ARGS) {
                    $argsParsed = join("/", $args);
                } else {
                    $argsParsed = "";
                }

                $linkMask .= $argsParsed;

                return rtrim($linkMask, "/");
            }

            if ($controller == $metadata[Visio\Route::CONTROLLER][Visio\Route::K_DEFAULT] && $action == $metadata[Visio\Route::DOACTION][Visio\Route::K_DEFAULT]) {
                if (isset($val->flags[0]) && $val->flags[0] == Visio\Route::ALLOW_ARGS) {
                    $argsParsed = "/" . join("/", $args);
                } else {
                    $argsParsed = "";
                }

                $linkMask = $val->mask . $argsParsed;
                break;
            }
        }

        if ($linkMask === null) {
            throw new Visio\Exception("Can not build reverse URL mask!");
        }

        return rtrim($linkMask, "/");
    }
}
