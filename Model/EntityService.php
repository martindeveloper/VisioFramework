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
 * Service layer for entity
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class EntityService extends Visio\Object {

    /**
     * Current entity
     *
     * @var Visio\Model\Entity $entity
     */
    protected $entity;

    /**
     * Active data access
     *
     * @var Visio\Model\DataAccess
     */
    protected $dataAccess;

    /**
     * __construct()
     *
     * @param Visio\Model\Entity $entity
     * @param Visio\Model\DataAccess $dataAccess
     */
    public function __construct(Visio\Model\Entity $entity, Visio\Model\DataAccess $dataAccess) {
        $this->entity = $entity;
        $this->dataAccess = $dataAccess;
    }

}
