<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Utilities;

use Visio;

/**
 * Class for calculating pagination
 *
 * @package Visio\Utilities
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Paginator extends Visio\Object {

    /**
     * @var int $itemsCount
     */
    private $itemsCount;

    /**
     * @var int $currentPage
     */
    private $currentPage = 1;

    /**
     * @var int $firstPage
     */
    private $firstPage = 1;


    /**
     * @var int $itemsPerPage
     */
    private $itemsPerPage = 10;

    /**
     * Set current page
     *
     * @param int $page
     * @return Paginator
     */
    public function setPage($page) {
        $this->currentPage = intval($page);
        return $this;
    }

    /**
     * Set total items count
     *
     * @param int $count
     * @return Paginator
     */
    public function setItemsCount($count) {
        $this->itemsCount = intval($count);
        return $this;
    }

    /**
     * Get first page
     *
     * @return int
     */
    public function getFirstPage() {
        return $this->firstPage;
    }

    /**
     * Set first page
     *
     * @param int $page
     * @return Paginator
     */
    public function setFirstPage($page) {
        $this->firstPage = intval($page);
        return $this;
    }

    /**
     * Set items per page
     *
     * @param $items
     */
    public function setItemsPerPage($items) {
        $this->itemsPerPage = $items;
    }

    /**
     * Get pages count
     *
     * @return float
     */
    public function getPagesCount() {
        return ceil($this->itemsCount / $this->itemsPerPage);
    }

    /**
     * Get items count for current page
     *
     * @return mixed
     */
    public function getCurrentItemsCount() {
        return min($this->itemsPerPage, $this->itemsCount - $this->getPageIndex() * $this->itemsPerPage);
    }

    /**
     * Get current page index
     *
     * @return mixed
     */
    public function getPageIndex() {
        $index = max(0, $this->currentPage - $this->firstPage);
        return min($index, max(0, $this->getPagesCount() - 1));
    }

    /**
     * Is current page first?
     *
     * @return bool
     */
    public function isFirst() {
        return ($this->getPageIndex() == 0);
    }

    /**
     * Is current page last?
     *
     * @return bool
     */
    public function isLast() {
        return ($this->getPagesCount() == $this->getPageIndex() + 1);
    }

    /**
     * Get offset
     *
     * @return mixed
     */
    public function getOffset() {
        return $this->getPageIndex() * $this->itemsPerPage;
    }
}