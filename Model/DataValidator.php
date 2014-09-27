<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Model;

use Visio, Visio\Model;

/**
 * Data validator for model
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class DataValidator extends Visio\Object implements \ArrayAccess {

    const EXPORT_PHP = "php", EXPORT_JS = "javascript";

    /**
     * Rules set
     *
     * @var array $rules
     */
    private $rules;

    /**
     * @var bool $valid
     */
    private $valid = false;

    /**
     * @var array $messages
     */
    private $messages = array();

    /**
     * __construct()
     *
     * @param array $rules
     */
    public function __construct(array $rules = array()) {
        $this->rules = $rules;
    }

    /**
     * Set new rules set
     *
     * @param array $rules
     */
    public function setRules(array $rules) {
        $this->rules = $rules;
        $this->valid = false;
    }

    /**
     * Add new rule
     */
    public function addRule(Model\IRule $rule) {
        $this->rules[] = $rule;
    }

    /**
     * Start validation
     *
     * @return Visio\Model\DataValidator
     * @throws Visio\Exception\Validator
     */
    public function validate() {
        foreach ($this->rules as $rule) {
            if (!$rule->test()) {
                $this->addMessage($rule);
            }
        }

        if (count($this->messages) == 0) {
            $this->valid = true;
        } else {
            throw new Visio\Exception\ValidatorFailed($this->getMessages());
        }

        return $this;
    }

    /**
     * Add message into messages buffer
     *
     * @param Visio\Model\IRule $rule
     * @throws Visio\Exception\Validator if message is not found
     */
    private function addMessage(Visio\Model\IRule $rule) {
        if (!isset($rule->message)) {
            throw new Visio\Exception\Validator("Undefined message for field " . $rule->fieldName . "!");
        }

        $this->messages[] = str_replace("\$value", $rule->fieldValue, $rule->message);
    }

    /**
     * Export rules into specified environment
     * @param string $environment
     * @return mixed
     */
    public function exportRules($environment) {
        switch ($environment) {
            default:
            case self::EXPORT_PHP:
                return $this->rules;
                break;

            case self::EXPORT_JS:
                return json_encode($this->rules);
                break;
        }
    }

    /**
     * Return post validation messages
     *
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Reset validator
     */
    public function reset() {
        $this->rules = null;
        $this->valid = false;
    }

    /**
     * Validation passed?
     * @return bool
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * @param mixed $index
     * @return bool
     */
    public function offsetExists($index) {
        return isset($this->rules[$index]);
    }

    /**
     * @param mixed $index
     * @return bool|mixed
     */
    public function offsetGet($index) {
        if ($this->offsetExists($index)) {
            return $this->rules[$index];
        }
        return false;
    }

    /**
     * @param mixed $index
     * @param mixed $value
     * @return bool|void
     */
    public function offsetSet($index, $value) {
        if ($index) {
            $this->rules[$index] = $value;
        } else {
            $this->rules[] = $value;
        }
        return true;

    }

    /**
     * @param mixed $index
     * @return bool|void
     */
    public function offsetUnset($index) {
        unset($this->rules[$index]);
        return true;
    }

    /**
     * @return mixed
     */
    public function getRules() {
        return $this->rules;
    }
}
