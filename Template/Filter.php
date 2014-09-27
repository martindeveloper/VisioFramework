<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template;

use Visio;

/**
 * Default filters handler class.
 * Can call filter on variable or register own filter on-the-fly.
 *
 * @package Visio\Template
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Filter extends Visio\Object {

    private $filters = array();

    /**
     * __construct()
     *
     * @param array $filters
     */
    public function __construct(array $filters) {
        $this->registerDefaultFilters($filters);
    }

    /**
     * registerDefaultFilters()
     *
     * @param array $filters
     * @return bool
     */
    public function registerDefaultFilters(array $filters) {
        foreach ($filters as $alias => $filter) {
            $object = new $filter;
            $this->registerFilter(array(&$object,
                                        'filter'), strtolower($alias));
        }

        return true;
    }

    /**
     * registerFilter()
     *
     * @param mixed $filter
     * @param string $alias
     */
    public function registerFilter($filter, $alias) {
        if ($this->filterExist($alias)) {
            throw new Visio\Exception\Template("Filter '" . $filter . "' is already registered to class '" . get_class($this->filters[$filter]) . "'!");
        } else {
            //array($obj, 'method', array('param', 'param2'));
            if (is_array($filter)) {
                $filterArray = array("class" => $filter[0],
                                     "method" => $filter[1],
                                     "additional" => array());

                if (isset($filter[2]) && is_array($filter[2])) {
                    $filterArray['additional'] = $filter[2];
                }
                $this->filters[$alias] = $filterArray;
            }

            //TODO: Support for Visio\Callback
            //if ($filter instanceof Visio\Callback) {
            //
            //}
        }
    }

    /**
     * filterValue()
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    public function filterValue($value, $type = "html") {
        //$value = (string )$value;

        $type = strtolower($type);
        $type = trim($type, '|');
        if (strpos($type, "\x7c") === false) {
            //Only one filter
            $value = $this->process($value, $type);
        } else {
            //More filters found
            $filters = explode('|', $type);
            foreach ($filters as $filter) {
                $value = $this->process($value, $filter);
            }
        }

        return $value;
    }

    /**
     * process()
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    private function process($value, $type) {
        if ($this->filterExist($type)) {
            array_unshift($this->filters[$type]['additional'], $value);
            $result = call_user_func_array(array($this->filters[$type]['class'],
                                                 'filter'), $this->filters[$type]['additional']);

            unset($this->filters[$type]['additional'][0]); //delete value from additional params

            return $result;
        } else {
            throw new Visio\Exception\Template("Filter '" . $type . "' not found!");
        }
    }

    /**
     * filterExist()
     *
     * @param string $filterName
     * @return bool
     */
    public function filterExist($filterName) {
        return isset($this->filters[$filterName]);
    }

}