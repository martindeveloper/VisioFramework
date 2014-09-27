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
 * Route class for Visio\Router.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Route extends Visio\Object implements Visio\Router\IRouter {

    const FIXED = '{1}';
    const OPTIONAL = '?';

    /**
     * @var array $metadata
     */
    private $metadata = array();

    /**
     * @var array $flags
     */
    private $flags = array();

    /**
     * @var string $regularExpression
     */
    private $regularExpression;

    /**
     * @var integer $_in
     */
    private $_in;

    /**
     * @var string $mask
     */
    private $mask;

    /**
     * @var bool $universal
     */
    public $universal = false;

    /**
     * __construct()
     *
     * @param string $mask
     * @param array $metadata
     * @param array $flags
     */
    public function __construct($mask, $metadata = array(), $flags = array()) {
        $this->mask = $mask;

        if (!empty($metadata) && is_string($metadata)) {
            $metaCheck = strrpos($metadata, ":");

            if (!strrpos($metadata, ":")) {
                throw new Visio\Exception\General("Unexpected \$metadata format.");
            }

            $metadata = array(self::CONTROLLER => substr($metadata, 0, $metaCheck),
                              self::DOACTION => $metaCheck === strlen($metadata) - 1 ? "default" : substr($metadata, $metaCheck + 1));
        }

        if (!is_array($metadata)) {
            $metadata = Visio\Router::$defaults;
        }

        $this->flags = $flags;
        $this->setMask($mask, $metadata);
    }

    /**
     * connect()
     *
     * @param Visio\Http\Request $httpRequest
     * @return mixed
     */
    public function connect(Visio\Http\Request $httpRequest) {
        $metadata = $this->metadata;
        $metadata[self::NAMES] = array();

        if (empty($metadata[self::LANG])) {
            $metadata[self::LANG][self::K_VALUE] = self::DEFAULT_LANG;
        }

        if (!preg_match('#^' . $this->regularExpression . '$#', trim($httpRequest->getQueryIndex('query'), '/'), $uriParts)) {
            return false;
        }

        foreach ($uriParts as $name => $value) {
            if (is_numeric($name)) {
                continue;
            }

            $name = str_replace('___', '-', $name);

            if (isset($metadata[$name])) {
                $metadata[$name][self::K_VALUE] = $value;

                if ($name != self::CONTROLLER && $name != self::DOACTION && $name != self::LANG) {
                    $metadata[self::NAMES][] = $name;
                }
            }

            if ($name == self::ALLOW_ARGS) {
                $metadata[$name] = explode('/', $value);
            }
        }

        return $metadata;
    }

    /**
     * setMask()
     *
     * @param string $mask
     * @param array $metadata
     * @return void
     */
    private function setMask($mask, array $metadata) {
        foreach ($metadata as $name => $meta) {
            if (!is_array($meta)) {
                $metadata[$name] = array(self::K_FIXED => empty($meta) ? self::OPTIONAL : self::FIXED,
                                         self::K_DEFAULT => $meta);
            } else {
                $metadata[$name][self::K_FIXED] = empty($meta[self::K_DEFAULT]) ? self::OPTIONAL : self::FIXED;
            }
        }
        $parts = preg_split("/<([^> ]+) *([^>]*)>|(\[!?|\]|\s*\?.*)/", $mask, -1, PREG_SPLIT_DELIM_CAPTURE); # <param-name #pattern> or [ or ] or ?...

        $counter = count($parts) - 1;
        $this->regularExpression = '';

        $regularExpression = '';

        #Path part
        do {
            if ($counter < 0) {
                break;
            } elseif (empty($parts[$counter]) || substr($parts[$counter], 0, 1) == '?') {
                --$counter;
                continue;
            }

            if ($parts[$counter] == '[') {
                $regularExpression = '(?:' . $regularExpression;
                --$counter;
                $this->_in = 1;
            }

            if ($parts[$counter] == ']') {
                $regularExpression = ')?' . $regularExpression;
                --$counter;
                $this->_in = 0;
            }

            $pattern = substr($parts[$counter], 0, 1) == '#' ? substr($parts[$counter], 1) : null;
            if (!empty($pattern)) {
                --$counter;
            }
            $name = substr($parts[$counter], 0, 1) == ':' ? substr($parts[$counter], 1) : null;

            if (!empty($name) && !isset($metadata[$name])) {
                $metadata[$name] = array(self::K_FIXED => self::OPTIONAL,
                                         self::K_DEFAULT => null);
            }

            //TODO: Bug: Problem with ?
            if (!empty($name)) {
                $regularExpression = '(?P<' . str_replace('-', '___', $name) . '>' . (empty($pattern) ? '[^/]+' : $pattern) . ')' . ($this->_in == 1 ? $metadata[$name][self::K_FIXED] : self::OPTIONAL) . $regularExpression;
                --$counter;
                $regularExpression = preg_quote($parts[$counter], '#') . $regularExpression;
                --$counter;
            } else {
                $regularExpression = preg_quote($parts[$counter], '#') . $regularExpression;
                --$counter;
            }

            //TODO: Host part
        } while (true);

        #Visio\Route::ALLOW_ARGS
        if (in_array(self::ALLOW_ARGS, $this->flags)) {
            $regularExpression .= '(?:/(?P<' . self::ALLOW_ARGS . '>[^\?]+)*)' . self::OPTIONAL;
        }

        $this->regularExpression = $regularExpression . $this->regularExpression;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getMask() {
        return $this->mask;
    }

    /**
     * @return string
     */
    public function getMetadata() {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getFlags() {
        return $this->flags;
    }
}
