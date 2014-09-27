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
 * Class for adress at Visio\Mailer
 *
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Address extends Visio\Object implements Visio\Mailer\IAddress {

    protected $raw;
    private $name;

    /**
     * __construct()
     *
     * @param string $address
     * @param string $name
     * @param bool $validate
     */
    public function __construct($address, $name = null, $validate = true) {
        if ($validate == true && $this->validate($address) === true) {
            $this->raw = $address;
        } else {
            throw new Visio\Exception\InvalidFormat("The address '" . $address . "' have invalid format!");
        }

        $this->name = $name;
    }

    /**
     * getUsername()
     *
     * @return string
     */
    public function getUsername() {
        preg_match("/(.*?)\@(.*)/iu", $this->raw, $matches);

        return $matches[1];
    }

    /**
     * getHostname()
     *
     * @return string
     */
    public function getHostname() {
        preg_match("/(.*?)\@(.*)/iu", $this->raw, $matches);

        return $matches[2];
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * getAddress()
     *
     * @return string
     */
    public function getAddress() {
        return $this->raw;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return $this->raw;
    }

    /**
     * validate()
     *
     * @param string $adress
     * @return bool
     */
    private function validate($adress) {
        if (preg_match("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/iu", $adress)) {
            return true;
        }

        return false;
    }

}