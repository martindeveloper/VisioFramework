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
 * Add filter for (x)HTML template context. Alias html.
 *
 * @package Visio\Template\Filter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Html extends Visio\Object implements Visio\Template\IFilter {

    public function filter($value) {
        return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }

}