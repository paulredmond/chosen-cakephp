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
     * Default configuration options.
     */
    protected $defaults = array(
        'class' => 'chzn-select',
        'safe' => true
    );
    
    /**
     * Runtime configuration
     */
    protected $settings = array();
    
    public function __construct(View $view, $settings = array())
    {
		parent::__construct($view, $settings);
		$this->settings = array_merge($this->defaults, (array) $settings);
		$this->debug = Configure::read('debug') ? true : false;
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
        
        $class = $this->settings['class'];
        
        if (isset($attributes['class']) === false) {
            $attributes['class'] = $class;
        }
        else if (strstr($attributes['class'], $class) === false) {
            $attributes['class'] .= " {$class}";
        }
        
        return $this->Form->select($name, $options, $attributes);
    }
    
    public function afterRender()
    {
        if (false === $this->load) {
            return;
        }
        
        $script = 'chosen.jquery.%s';
        $script = sprintf($script, $this->debug === true ? 'js' : 'min.js');
        
        $class = $this->settings['class'];
        
        $this->Html->css('/chosen/chosen/chosen.css', null, array('inline' => false));
        $this->Html->script("/chosen/chosen/{$script}", array('inline' => false));
        $this->Html->scriptBlock("
            $(document).ready(function(){
                $('.{$class}').chosen();
            });",
            array('inline' => false, 'safe' => $this->settings['safe'])
        );
    }
}
