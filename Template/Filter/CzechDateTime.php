<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template\Filter;

use Visio;

/**
 * Add filter for Czech date and time.
 *
 * @package Visio\Template\Filter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class CzechDateTime extends Visio\Object implements Visio\Template\IFilter {

    public function filter($value) {
        return date('j.n. Y H:i:s', strtotime($value));
    }

}