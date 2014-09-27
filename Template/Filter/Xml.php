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
 * Add filter for XML template context. Alias xml.
 *
 * @package Visio\Template\Filter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Xml extends Visio\Object implements Visio\Template\IFilter {

    public function filter($value) {
        return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }

}