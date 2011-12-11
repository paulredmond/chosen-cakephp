<?php

App::uses('controller', 'Controller');
App::uses('View', 'View');
App::uses('ChosenHelper', 'Chosen.View/Helper');

class ChosenHelperTest extends CakeTestCase {
    
    public $Chosen = null;
    
    public function setUp()
    {
        parent::setUp();
        $View = new View(new Controller());
        $this->Chosen = new ChosenHelper($View);
    }
    
}