<?php

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('ChosenHelper', 'Chosen.View/Helper');

class TestView extends View {
    public function getScripts()
    {
        return $this->_scripts;
    }
}

class ChosenHelperTest extends CakeTestCase {
    
    public $Chosen = null;
    
    public function setUp()
    {
        parent::setUp();
        $this->View = new TestView(new Controller());
        $this->Helper = new Helper($this->View);
        $this->Helper->request = new CakeRequest(null, false);
        $this->Helper->request->webroot = '';
        $this->Html = new HtmlHelper($this->View);
        $this->Form = new FormHelper($this->View);
        $this->Chosen = new ChosenHelper($this->View);
        $this->Chosen->Form = $this->Form;
        $this->Chosen->Html = $this->Html;
        $this->Chosen->Html->request = $this->Form->request = $this->Helper->request;
    }
    
    public function getNewHelperInstance($settings = array())
    {
        return new ChosenHelper(new View(new Controller()), $settings);
    }
    
    public function getSelectInput($options = array())
    {
        return $this->Chosen->select('Article.category', array(1 => 'Option 1', 2 => 'Option 2'), $options);
    }
    
    public function testSelectTag()
    {
        
        $dom = new DomDocument();
        $dom->loadHTML($this->getSelectInput());
        $this->assertTag(array(
            'tag' => 'select',
            'children' => array(
                'count' => 3, // Account for default empty.
                'only' => array('tag' => 'option')
            )
        ), $dom, '<select> should exist with two child option tags');
        
        // Calling select will set ChosenHelper::$load to true
        // This means that chosen vendor scripts/assets will be load on render.
        $this->assertTrue($this->Chosen->getLoadStatus());
    }
    
    public function testSelectOptions()
    {
        $placeholderText = 'Please select option';
        $dom = new DomDocument();
        $html = $this->getSelectInput(array('data-placeholder' => $placeholderText));
        $dom->loadHTML($html);
        $select = $dom->getElementsByTagName('select')->item(0);
        $this->assertNotNull($select);
        $this->assertInstanceOf('DOMElement', $select);
        $this->assertEqual($placeholderText, $select->getAttribute('data-placeholder'));
    }
    
    public function testChosenClassAttribute()
    {
        // Make sure the chosen class attribute exists
        $class = $this->Chosen->getSetting('class');
        $dom = new DomDocument();
        $html = $this->getSelectInput();
        $dom->loadHTML($html);
        $select = $dom->getElementsByTagName('select')->item(0);
        $this->assertNotNull($select);
        $this->assertInstanceOf('DOMElement', $select);
        $this->assertEqual($class, $select->getAttribute('class'));
        
        // Adding a class to the options array should not remove the chosen class
        $customClass = 'my-custom-class';
        $dom = new DomDocument();
        $html = $this->getSelectInput(array('class' => $customClass));
        $dom->loadHTML($html);
        $select = $dom->getElementsByTagName('select')->item(0);
        $this->assertNotNull($select);
        $this->assertInstanceOf('DOMElement', $select);
        $this->assertEqual("{$customClass} {$class}", $select->getAttribute('class'));
    }
    
    public function testAfterRenderMethod()
    {
        $html = $this->getSelectInput();
        $this->Chosen->afterRender();
        $scripts = $this->View->getScripts();
        
        $expected = array(
			array('link' => array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => '/chosen/chosen/chosen.css')),
			array('script' => array('type' => 'text/javascript', 'src' => '/chosen/chosen/chosen.jquery.js')),
			'/script',
			array('script' => array('type' => 'text/javascript')),
		);
		$this->assertCount(3, $scripts);
		$this->assertTags(implode("\n", $scripts), $expected);

		// Third script is a little trickier to test
		$class = $this->Chosen->getSetting('class');
		$script = $scripts[2];
		// Check for a jQuery domready
		$this->assertRegExp("/\\$\(\s*document\s*\)\.ready\(\s*function\s*\(\){/", $script);
		// Make sure script is calling chosen() method with the configured className
		$this->assertRegExp("/\\$\(['\"]\.{$class}['\"]\)\.chosen\(\)/", $script);
		// Make sure domready call is closed properly
		$this->assertRegExp("/}\s*\);/", $script);
    }
    
    public function testHelperConstruction()
    {
        $helper = $this->Chosen;
        
        // Make sure defaults are working
        $this->assertEqual($helper->getDefaults(), $helper->getSettings());
        
        // Test when custom settings are configured.
        $custom = $this->getNewHelperInstance(array(
            'class' => 'my-custom-chosen'
        ));
        
        // Custom settings should be merged in
        $this->assertNotEquals($custom->getSettings(), $custom->getDefaults());
        
        // Preserve current debug seting
        $debug = Configure::read('debug');
        
        // Test to see if debug is on
        Configure::write('debug', 2);
        $debugHelper = $this->getNewHelperInstance();
        $this->assertTrue($debugHelper->getDebug());
        
        // Debug should be false
        Configure::write('debug', 0);
        $noDebugHelper = $this->getNewHelperInstance();
        $this->assertFalse($noDebugHelper->getDebug());
        
        // Restore current debug setting
        Configure::write('debug', $debug);
        
        // Make sure Chosen helper has instance of each item in $helpers
        $this->assertInstanceOf('ChosenHelper', $this->Chosen);
        foreach ($this->Chosen->helpers as $name) {
            $this->assertInstanceOf($name . 'Helper', $this->Chosen->{$name});
        }
    }
    
    public function testGetSettingMethod()
    {
        $helper = $this->Chosen;
        
        $expected = $helper->getDefaults();
        $expected = $expected['class'];
        
        // Setting returned should not be null
        $this->assertNotNull($helper->getSetting('class'));
        
        // getSetting should return the default class
        $this->assertEquals($expected, $helper->getSetting('class'));
        
        // Keys that are not set should return null
        $this->assertNull($helper->getSetting('setting-does-not-exist'));
    }
    
    public function testGetSettingsMethod()
    {
        $helper = $this->Chosen;
        $settings = $helper->getSettings();
        
        // Make sure the type is correct
        $this->assertInternalType('array', $settings);
        
        // Settings should not be empty
        $this->assertNotEmpty($settings);
    }
    
    public function testGetDefaultsMethod()
    {
        $helper = $this->Chosen;
        
        $defaults = $helper->getDefaults();
        
        // Make sure the getDefaults returns an array
        $this->assertInternalType('array', $defaults);
        
        // Defaults are not empty
        $this->assertNotEmpty($defaults);
    }
}