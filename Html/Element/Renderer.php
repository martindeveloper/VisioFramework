<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Html\Element;

use Visio;

/**
 * Renderer for HTML element.
 *
 * @package Visio\Html\Element
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Renderer extends Visio\Object {

    const MODE_XHTML = 1;
    const MODE_HTML4 = 2;
    const MODE_HTML5 = 3;

    /**
     * @var integer $mode
     */
    private $mode = 0;

    /**
     * @var Visio\Html\Element $element
     */
    private $element;

    /**
     * __construct()
     *
     * @param Visio\Html\Element $element
     * @param integer $mode
     */
    public function __construct(Visio\Html\Element $element, $mode = self::MODE_XHTML) {
        $this->element = $element;
        $this->mode = (integer)$mode;
    }

    /**
     * setMode()
     *
     * @param integer $mode
     */
    public function setMode($mode) {
        $this->mode = (integer)$mode;
    }

    /**
     * render()
     *
     * @return string
     */
    public function render() {
        $output = new Visio\Utilities\StringBuilder();
        $attributes = $this->element->getAttributes();

        $output->append($this->renderInject(Visio\Html\Element::INJECT_BEFORE));

        $output->append("<" . $this->element->getName() . " ");

        //$content = (isset($attributes['content']) ? $attributes['content'] : '');
        unset($attributes['content']);

        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) {
                $value = implode(" ", $value);
            }

            $output->append($attribute . '="' . $value . '" ');
        }

        $pairTag = $this->element->isPair();

        if ($pairTag === true) {
            $output->rtrim(" ");
            $output->append(">");

            $output->append($this->renderInject(Visio\Html\Element::INJECT_TOP));

            $output->append($this->element->getInnerHTML());

            $output->append($this->renderInject(Visio\Html\Element::INJECT_BOTTOM));

            $output->append("</" . $this->element->getName() . ">");
        } else {
            $output->append(($this->mode == self::MODE_HTML4) ? ">" : "/>");
        }

        $output->appendLine($this->renderInject(Visio\Html\Element::INJECT_AFTER));

        return (string)$output;
    }

    /**
     * renderInject()
     *
     * @param integer $position
     * @return string
     */
    private function renderInject($position) {
        $stringBuilder = new Visio\Utilities\StringBuilder();
        $injections = $this->element->getInjections();

        if (!empty($injections[$position])) {
            foreach ($injections[$position] as $element) {
                $stringBuilder->appendLine($element);
            }
        }

        return (string)$stringBuilder;
    }

}
