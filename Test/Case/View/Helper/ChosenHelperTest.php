<?php

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('ChosenHelper', 'Chosen.View/Helper');
App::uses('FormHelper', 'View/Helper');

class ChosenHelperTest extends CakeTestCase {
    
    public $Chosen = null;
    
    public function setUp()
    {
        parent::setUp();
        $View = new View(new Controller());
        $this->Chosen = new ChosenHelper($View);
        $form =& $this->Chosen->Form;
        $form->request = new CakeRequest('/', false);
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
    
    public function testChosenClass()
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
    
    public function testGetSettings()
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