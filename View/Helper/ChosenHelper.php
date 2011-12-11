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
     * $loaded
     * If a chosen select element was called, load up the scripts.
     */
    private $loaded = false;
    
    /**
     * Determine if debug is disabled/enabled
     */
    private $debug = false;
    
    /**
     * Default configuration options.
     */
    protected $options = array(
        'class' => 'chzn-select',
        'safe' => true
    );
    
    /**
     * Runtime configuration
     */
    protected $settings = array();
    
    public function __construct($options=null)
    {
		parent::__construct($options);
		$this->settings = array_merge($this->options, (array) $options);
		$this->debug = Configure::read('debug') ? true : false;
	}
    
    /**
     * Chosen select element.
     */
    public function select($name, $options = array(), $attributes = array())
    {
        if (false === $this->loaded) {
            $this->loaded = true;
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
        if (false === $this->loaded) {
            return;
        }
        
        $script = 'chosen.jquery.%s';
        $script = sprintf($script, $this->debug === true ? 'js' : 'min.js');
        
        $class = $this->settings['class'];
        
        $this->Html->css($this->webroot() . '/chosen/chosen.css', null, array('inline' => false));
        $this->Html->script($this->webroot() . "/chosen/{$script}", array('inline' => false));
        $this->Html->scriptBlock("
            $(document).ready(function(){
                $('.{$class}').chosen();
            })",
            array('inline' => false, 'safe' => $this->settings['safe'])
        );
    }
    
    /**
     * The plugin's webroot folder name.
     * 
     * Configured in this plugin's bootstrap.php file.
     * Ideally, symlink this path to the webroot.
     * 
     * @access public
     * @return string
     */
    public function webroot()
    {
        $webroot = Configure::read('Chosen.webroot');
        
        return null === $webroot ? $webroot : "/{$webroot}";
    }
}
