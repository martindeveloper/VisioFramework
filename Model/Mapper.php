<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Model;

use Visio;

/**
 * Mapper for model
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Mapper extends Visio\Object implements IMapper {

    /**
     * Master database object
     *
     * @var mixed $database
     */
    public $database;

    /**
     * Set master database object to model
     *
     * @param mixed $dbo
     */
    public function setDatabase($dbo) {
        $this->database = $dbo;
    }

}
