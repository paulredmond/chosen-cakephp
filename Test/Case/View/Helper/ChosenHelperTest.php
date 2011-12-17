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
    
    public function getSelectInput()
    {
        $id = 'my_id';
        return $this->Chosen->select('Article.select', array(1 => 'Option 1', 2 => 'Option 2'), array('id' => $id));
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
        // $select = $dom->getElementsByTagName('select');
        // $this->assertLength();

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