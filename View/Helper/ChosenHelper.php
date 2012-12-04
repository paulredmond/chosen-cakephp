<?php
/**
 * Chosen Helper File
 *
 * Copyright (c) 2011 Paul Redmond
 *
 * Distributed under the terms of the MIT License.
 *
 * PHP Version 5
 * CakePHP Version 2.x
 *
 * @package chosen
 * @subpackage chosen.views.helpers
 * @copyright 2011 Paul Redmond <paulrredmond@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/paulredmond/chosen-cakephp
 *
 * HarvestHQ Chosen JQuery/Prototype plugin.
 * @link https://github.com/harvesthq/chosen
 *
 */
class ChosenHelper extends AppHelper
{
    public $helpers = array('Html', 'Form');

    /**
     * $load
     * If a chosen select element was called, load up the scripts.
     */
    private $load = false;

    /**
     * Determine if debug is disabled/enabled
     */
    private $debug = false;

    /**
     * @var \View
     */
    private $view = null;

    /**
     * Default configuration options.
     */
    protected $defaults = array(
        'framework' => 'jquery',
        'class' => 'chzn-select',
    );

    /**
     * Runtime configuration
     */
    protected $settings = array();

    public function __construct(View $view, $settings = array())
    {
		parent::__construct($view, $settings);
		$this->view = $view;
		$this->settings = array_merge($this->defaults, (array) $settings);
		$this->debug = Configure::read('debug') ? true : false;

		if (!in_array($fw = $this->getSetting('framework'), array('jquery', 'prototype'))) {
		    throw new LogicException(sprintf('Configured JavaScript framework "%s" is not supported. Only "jquery" or "prototype" are valid options.', $fw));
		}
	}

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSetting($setting)
    {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }

        return null;
    }

    public function getDefaults()
    {
        return (array) $this->defaults;
    }

    public function getDebug()
    {
        return (boolean) $this->debug;
    }

    public function getLoadStatus()
    {
        return (bool) $this->load;
    }

    /**
     * Chosen select element.
     */
    public function select($name, $options = array(), $attributes = array())
    {
        if (false === $this->load) {
            $this->load = true;
        }

        $class = $this->getSetting('class');

        // Use these locally to do some checking...still pass attributes to FormHelper.
        $multiple = isset($attributes['multiple']) ? $attributes['multiple'] : false;
        $deselect = isset($attributes['deselect']) ? $attributes['deselect'] : false;

        // Chosen only supports deselect on single selects.
        // @todo write a test and configure
        if ($deselect === true && $multiple === false) {
            $class .= '-deselect';
            unset($attributes['deselect']);
        }

        if (isset($attributes['class']) === false) {
            $attributes['class'] = $class;
        }
        else if (strstr($attributes['class'], $class) === false) {
            $attributes['class'] .= " {$class}";
        }

        return $this->Form->select($name, $options, $attributes);
    }

    public function afterRender($viewFile)
    {
        if (false === $this->load) {
            return;
        }

        switch ($this->getSetting('framework')) {
            case 'prototype':
                $elm = 'prototype-script';
                $script = 'chosen.proto.%s';
            break;

            case 'jquery':
            default:
                $elm = 'jquery-script';
                $script = 'chosen.jquery.%s';
            break;
        }

        // 3rd party assets
        $script = sprintf($script, $this->debug === true ? 'js' : 'min.js');
        $this->Html->css('/chosen/chosen/chosen.css', null, array('inline' => false));
        $this->Html->script("/chosen/chosen/{$script}", array('inline' => false));

        // Add the script.
        if (false !== $block = $this->getElement($elm)) {
            $this->Html->scriptBlock($block, array('inline' => false));
        }
    }

    /**
     * Gets the Plugin's element file based on JS framework being used.
     * Accommodates 2.0.0 and 2.1.0 View::element() API changes.
     *
     * @param $element string Name of the plugin element to use.
     * @return string rendered javascript block based on the JS framework element.
     */
    protected function getElement($element)
    {
        $block = false;
        $version = Configure::version();
        $class = $this->getSetting('class');

        if (version_compare($version, '2.1.0', '>=')) {
            $block = $this->view->element('Chosen.' . $element, array('class' => $class));
        } else if (version_compare($version, '2.1.0', '<')) {
            $block = $this->view->element($element, array('class' => $class), array('plugin' => 'Chosen'));
        }

        return $block;
    }
}
