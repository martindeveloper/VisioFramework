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
 * Class for working with \DateTime.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class DateTime extends \DateTime {

    /**
     * Minutes in seconds
     *
     * @const Visio\DateTime::MINUTE
     */
    const MINUTE = 60;

    /**
     * Hour in seconds
     *
     * @const int Visio\DateTime::MINUTE
     */
    const HOUR = 3600;

    /**
     * Day in seconds
     *
     * @const int Visio\DateTime::DAY
     */
    const DAY = 86400;

    /**
     * Week in seconds
     *
     * @const int Visio\DateTime::WEEK
     */
    const WEEK = 604800;

    /**
     * Month in seconds
     *
     * @const int Visio\DateTime::MONTH
     */
    const MONTH = 2678400;

    /**
     * Year in seconds
     *
     * @const int Visio\DateTime::YEAR
     */
    const YEAR = 32140800;

    /**
     * @var Visio\DateTime $instance
     */
    private static $instance;

    /**
     * __construct()
     *
     * @param string $timezone
     */
    public function __construct($timezone = "") {
        parent::__construct();

        if (!empty($timezone)) {
            $this->setTimezone(new \DateTimeZone($timezone));
        }
    }

    /**
     * Visio\DateTime::getInstance()
     *
     * @return Visio\DateTime
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * __clone()
     */
    private function __clone() {

    }

}
