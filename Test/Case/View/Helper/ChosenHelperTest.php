<?php

/**
 * This file is part of the Chosen Helper Plugin.
 *
 * Copyright Paul Redmond - https://github.com/paulredmond
 *
 * @link https://github.com/paulredmond/chosen-cakephp
 * @license http://paulredmond.mit-license.org/ The MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('ChosenHelper', 'Chosen.View/Helper');

class TestView extends View
{
    public function getScripts()
    {
        return $this->_scripts;
    }
}

class ChosenHelperTest extends CakeTestCase
{

    public $Chosen = null;

    public function setUp()
    {
        parent::setUp();
        $this->View                     = new TestView(new Controller());
        $this->Helper                   = new Helper($this->View);
        $this->Helper->request          = new CakeRequest(null, false);
        $this->Helper->request->webroot = '';
        $this->Html                     = new HtmlHelper($this->View);
        $this->Form                     = new FormHelper($this->View);
        $this->Chosen                   = new ChosenHelper($this->View);
        $this->Chosen->Form             = $this->Form;
        $this->Chosen->Html             = $this->Html;
        $this->Chosen->Html->request    = $this->Form->request = $this->Helper->request;
    }

    public function getNewHelperInstance($settings = array())
    {
        return new ChosenHelper(new View(new Controller()), $settings);
    }

    public function getSelectInput($options = array())
    {
        return $this->Chosen->select('Article.category', array(1 => 'Option 1', 2 => 'Option 2'), $options);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Configured JavaScript framework "does_not_exist" is not supported. Only "jquery" or "prototype" are valid options.
     */
    public function testInvalidFrameworkLogicException()
    {
        $helper = $this->getNewHelperInstance(array('framework' => 'does_not_exist'));
    }

    public function testIsSupportedFrameworkMethod()
    {
        $message = "The framework setting '%s' should be valid, but is not.";
        $helper = $this->getNewHelperInstance();
        $this->assertTrue($helper->isSupportedFramework('jquery'), sprintf($message, 'jquery'));
        $this->assertTrue($helper->isSupportedFramework('prototype'), sprintf($message, 'prototype'));
        $this->assertfalse($helper->isSupportedFramework('PROTOTYPE'), "Uppercase 'PROTOTYPE' should not be a valid framework.");
        $this->assertFalse($helper->isSupportedFramework('not_supported'), "The framework setting 'not_supported' should not be valid.");
    }

    public function testSelectTag()
    {

        $dom = new DomDocument();
        $dom->loadHTML($this->getSelectInput());
        $this->assertTag(array(
            'tag'      => 'select',
            'children' => array(
                'count' => 3, // Account for default empty.
                'only'  => array('tag' => 'option')
            )
        ), $dom, '<select> should exist with two child option tags');

        // Calling select will set ChosenHelper::$load to true
        // This means that chosen vendor scripts/assets will be load on render.
        $this->assertTrue($this->Chosen->getLoadStatus());
    }

    public function testSelectOptions()
    {
        $placeholderText = 'Please select option';
        $dom             = new DomDocument();
        $html            = $this->getSelectInput(array('data-placeholder' => $placeholderText));
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
        $dom   = new DomDocument();
        $html  = $this->getSelectInput();
        $dom->loadHTML($html);
        $select = $dom->getElementsByTagName('select')->item(0);
        $this->assertNotNull($select);
        $this->assertInstanceOf('DOMElement', $select);
        $this->assertEqual($class, $select->getAttribute('class'));

        // Adding a class to the options array should not remove the chosen class
        $customClass = 'my-custom-class';
        $dom         = new DomDocument();
        $html        = $this->getSelectInput(array('class' => $customClass));
        $dom->loadHTML($html);
        $select = $dom->getElementsByTagName('select')->item(0);
        $this->assertNotNull($select);
        $this->assertInstanceOf('DOMElement', $select);
        $this->assertEqual("{$customClass} {$class}", $select->getAttribute('class'));
    }

    public function testAfterRenderMethod()
    {
        $html = $this->getSelectInput();
        $this->Chosen->afterRender('fake');
        $scripts = $this->View->Blocks->get('script');
        $css     = $this->View->Blocks->get('css');

        $expected = array(
            array('script' => array('type' => 'text/javascript', 'src' => '/chosen/chosen/chosen.jquery.js')),
            '/script',
            array('script' => array()), // Bare <script> tag
        );

        $this->assertTags($scripts, $expected);

        $expected = array('link' => array(
            'rel'  => 'stylesheet',
            'type' => 'text/css',
            'href' => '/chosen/chosen/chosen.css'
        ));
        $this->assertTags($css, $expected);

        // Third script is a little trickier to test
        $class = $this->Chosen->getSetting('class');

        // Check for a jQuery domready
        $this->assertRegExp("/\\$\(\s*document\s*\)\.ready\(\s*function\s*\(\){/", $scripts);
        // Make sure script is calling chosen() method with the configured className
        $this->assertRegExp("/\\$\(['\"]\.{$class}['\"]\)\.chosen\(\)/", $scripts);
        // Make sure domready call is closed properly
        $this->assertRegExp("/}\s*\);/", $scripts);
    }

    public function testHelperConstruction()
    {
        $helper = $this->Chosen;

        // Test when custom settings are configured.
        $custom = $this->getNewHelperInstance(array(
            'class' => 'my-custom-chosen'
        ));

        // Preserve current debug setting
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

        $expected = $helper->getSettings();
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
        $helper   = $this->Chosen;
        $settings = $helper->getSettings();

        // Make sure the type is correct
        $this->assertInternalType('array', $settings);

        // Settings should not be empty
        $this->assertNotEmpty($settings);
    }

    public function testLoadScriptsCalledTwoTimes()
    {
        $this->Chosen->Html = $this->getMock('Html', array('css', 'script', 'scriptBlock'));
        $this->Chosen->Html->expects($this->once())
            ->method('css');

        $this->Chosen->loadScripts();
        $this->Chosen->loadScripts();
    }
}