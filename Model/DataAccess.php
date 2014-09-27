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
 * Data access for model
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class DataAccess extends Visio\Object implements IDataAccess {

    /**
     * Data mapper
     *
     * @var Visio\Model\IMapper $mapper
     */
    protected $mapper;

    /**
     * Translation object
     *
     * @var Visio\Translate
     */
    protected $translate;

    /**
     * Session object
     *
     * @var Visio\Session
     */
    protected $session;

    /**
     * ACL object
     *
     * @var Visio\Acl
     */
    protected $accessControl;

    /**
     * __construct()
     */
    public function __construct() {
    }

    /**
     * Set mapper to data access
     *
     * @param Visio\Model\IMapper $mapper
     */
    public function setMapper(Visio\Model\IMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Get current mapper
     *
     * @return Visio\Model\IMapper
     */
    public function getMapper() {
        return $this->mapper;
    }

    /**
     * Set translate object
     *
     * @param Visio\Translate $translate
     */
    public function setTranslate(Visio\Translate $translate) {
        $this->translate = $translate;
    }

    /**
     * Set session object
     *
     * @param Visio\Session $session
     */
    public function setSession(Visio\Session $session) {
        $this->session = $session;
    }

    /**
     * Set ACL object
     *
     * @param Visio\Acl $acl
     */
    public function setAccessControl(Visio\Acl $acl) {
        $this->accessControl = $acl;
    }

}
