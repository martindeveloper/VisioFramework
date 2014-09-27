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
 * Main exception class.
 * Support Visio\Events. Can build and show readable message depend on environment.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Exception extends \Exception {

    /**
     * Title of exception
     *
     * @var string $title
     */
    public $title = "Application runtime error";

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    /**
     * __construct()
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct($message, $code = E_NOTICE /*, \Exception $previous = null*/) {
        parent::__construct($message, (int)$code);

        try {
            @Visio\Events::dispatch("Exception-onThrow", array(&$message,
                                                               &$code,
                                                               (string)$this,
                                                               $this->getLine(),
                                                               $this->getFile()));
        } catch (Visio\Exception $ex) {
        }

        $this->message = str_replace("\$", "&#36;", $this->message);
    }

    /**
     * @param $environment
     * @return array
     */
    public function getBacktrace($environment) {
        $out = array();
        $callStack = Visio\Diagnostic::getBacktraceArray();

        $iteration = 1;

        foreach ($callStack as $call) {
            $args = "";
            if (isset($call["args"]) && is_array($call["args"]) && !empty($call["args"])) {
                foreach ($call["args"] as $key => $val) {

                    $data = $this->argumentType($val);
                    $args .= $key . " => " . $data . "\n";
                }
            } else {
                $args = "no arguments";
            }

            if ($environment != "development" && isset($call["file"])) {
                $call["file"] = substr($call["file"], strlen(ROOT));
            }

            $out[] = array("id" => $iteration,
                           "file" => (isset($call["file"]) ? $call["file"] : "-"),
                           "args" => str_replace("\$", "&#36;", $args),
                           "line" => (isset($call["line"]) ? $call["line"] : "-"),
                           "url" => "http://visio.pria.cz/doku.php/start?do=search&id=" . urlencode($call["function"]),
                           "function" => $call["function"]);
            $iteration++;
        }

        return $out;
    }

    /**
     * argumentType()
     *
     * @param mixed $val
     * @param integer $indent
     * @return string
     */
    private function argumentType($val, $indent = 1) {
        $maskedKeys = array("password",
                            "pass",
                            "paswd");

        $tab = str_repeat("	", $indent);

        if (is_object($val)) {
            return "object " . ($val instanceof Visio\Object) ? get_class($val) : get_class($val);
        }

        if (is_string($val)) {
            return "string " . htmlspecialchars($val, ENT_QUOTES, "utf-8");
        }

        if (is_int($val)) {
            return "integer " . intval($val);
        }

        if (is_float($val)) {
            return "float " . floatval($val);
        }

        if (is_array($val)) {
            $data = "Array { \n";

            foreach ($val as $aKey => $aVal) {
                $data .= $tab . $aKey . " => ";

                if (in_array($aKey, $maskedKeys, true)) {
                    $aVal = print_r($aVal, true);
                    $aVal = \str_repeat("*", \strlen($aVal));
                }

                $data .= $this->argumentType($aVal, ($indent + 1));
                $data .= "\n";
            }

            $data .= $tab . "}";

            return $data;
        }

        return htmlspecialchars((string)$val, ENT_QUOTES, "utf-8");
    }

    /**
     * getFilePreview()
     *
     * @param string $path
     * @param integer $line
     * @return mixed
     */
    public function getFilePreview($path, $line = 1) {
        if ($this->container instanceof Visio\DependencyInjection\IContainer) {
            $config = $this->container->applicationConfig;
            $show = $config->get("file", "Exception");
            $maxLines = max(1, intval($config->get("lines", "Exception")));
        } else {
            $show = false;
            $maxLines = 1;
        }
        $out = array();

        if ($show == true) {
            $file = Visio\FileSystem::readFile($path);

            if ($config->get("highlight", "Exception") != true || pathinfo($path, PATHINFO_EXTENSION) != "php") {
                $lines = explode("\n", $file);
            } else {
                $highlight = highlight_string($file, true);
                $highlight = str_replace("<code>", "", $highlight);
                $highlight = explode("\n", $highlight);

                $highlight[1] = $highlight[0] . $highlight[1];
                $highlight[1] .= $highlight[2];

                unset($highlight[2], $highlight[0]);

                $lines = explode("<br />", $highlight[1]);
            }

            $endLine = sizeof($lines);
            $start = intval(max(1, $line - floor($maxLines / 2)));
            $end = intval(min($endLine, ($line + floor($maxLines / 2))));

            for ($i = $start; $i <= $end; $i++) {
                $lines[$i - 1] = str_replace("\$", "&#36;", $lines[$i - 1]);
                if ($line == $i) {
                    $out[] = array('line' => $i,
                                   'source' => $lines[$i - 1],
                                   'class' => 'high');
                } else {
                    $out[] = array('line' => $i,
                                   'source' => $lines[$i - 1],
                                   'class' => '');
                }
            }
        } else {
            $out[] = array('line' => 'File preview is disabled!',
                           'code' => '',
                           'class' => '');
        }

        return $out;
    }

    /**
     * getGlobalData()
     */
    private function getGlobalData($source) {
        $post = array();

        foreach ($source as $key => $value) {
            $post[] = array("name" => $key,
                            "value" => $this->argumentType($value));
        }

        return $post;
    }

    /**
     * buildErrorMessage()
     */
    private function buildErrorMessage() {
        $environment = $this->container->applicationConfig->get('environment', 'Application');
        $templates = $this->container->applicationConfig->get('templates', 'Exception');

        if (!isset($templates[$environment]) || !is_file($templates[$environment])) {
            die("Fatal error: Can not load exception template on path: " . $templates[$environment]);
        }

        if ($environment != "development") {
            $this->file = substr($this->file, strlen(ROOT));
        }


        $tpl = new Visio\Template($templates[$environment], false, $this->container);

        $referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'Empty';

        $replace = array('description' => strip_tags($this->message),
                         'code' => Visio\Diagnostic\Debugger::translateErroCode($this->code) . ' (' . $this->code . ')',
                         'message' => trim(strip_tags($this->message), "!."),
                         'line' => $this->line,
                         'file' => $this->file,
                         'backtrace' => $this->getBacktrace($environment),
                         'vfversion' => Visio\Framework::VERSION,
                         'phpversion' => phpversion(),
                         'zendversion' => zend_version(),
                         'referer' => $referer,
                         'date' => Visio\DateTime::getInstance()->format("m/d/y H:i:s"),
                         'preview' => $this->getFilePreview($this->file, $this->line),
                         'postdata' => $this->getGlobalData($_POST),
                         'serverdata' => $this->getGlobalData($_SERVER),
                         'type' => get_class($this),
                         'memory' => Visio\Diagnostic::getMemoryUsage(),
                         'memoryLimit' => Visio\Diagnostic::getMemoryLimit(),
                         'scriptTime' => @number_format(Visio\Diagnostic::getStopwatchDelta("ScriptExecutionTime"), 2) . " ms",
                         'title' => $this->title);

        $tpl->setArray($replace);

        return $tpl;
    }

    /**
     * showErrorMessage()
     */
    public function showErrorMessage() {
        Visio\Events::dispatch("Exception-onShow", array((string)$this->message,
                                                         (int)$this->code,
                                                         (string)$this));

        if (Visio\Http::getInstance()->request->isAjax()) {
            $tpl = json_encode(array('state' => 'exception',
                                     'message' => $this->message,
                                     'code' => $this->code,
                                     'file' => $this->file,
                                     'line' => $this->line,
                                     'type' => (string)$this));
        } else {
            $container = new Visio\DependencyInjection\Container();
            $container->initialize();

            $this->container = $container;
            $tpl = $this->buildErrorMessage();
        }

        try {
            Visio\Http::getInstance()->response->addHeaderRaw("HTTP/1.1 Internal Server Error", true, 500);
        } catch (Visio\Exception $exception) {
        }

        @ob_clean();
        @ob_flush();

        echo $tpl;

        unset($tpl);

        die();
    }

    /**
     * getErrorMessage()
     * @return string
     */
    public function getErrorMessage() {
        $tpl = $this->buildErrorMessage();
        return $tpl->getOutput();
    }

    /**
     * getClassName()
     *
     * @return string
     */
    public function getClassName() {
        return get_class($this);
    }

    /**
     * setMessage()
     *
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * __toString()
     * @return string
     */
    public function __toString() {
        return get_class($this);
    }

}
