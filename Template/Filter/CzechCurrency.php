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
 * Add filter for Czech currency.
 *
 * @package Visio\Template\Filter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class CzechCurrency extends Visio\Object implements Visio\Template\IFilter {

    public function filter($value) {
        setlocale(LC_ALL, 'cs_CZ.UTF8');
        return money_format('%n', $value);
    }

}