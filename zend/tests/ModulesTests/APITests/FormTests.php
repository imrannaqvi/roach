<?php
namespace ModulesTests\APITests\Form;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class FormTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $namespace = 'API\Form';
	protected $baseClass = 'Form';
	protected $forms = array(
		'Login'
	);
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	/**
	* @expectedException	Zend\ServiceManager\Exception\ServiceNotFoundException
	*/
	public function test_BaseClassAsService()
	{
		//Form base class SHOULD NOT BE accessible as service
		$this->serviceManager->get($this->namespace.'\\'.$this->baseClass);
	}
	
	//all forms SHOULD BE accessible as a service
	public function test_FormsAsService()
	{
		for($i=0; $i<count($this->forms); $i++) {
			$form = $this->namespace.'\\'.$this->forms[$i];
			$instance = $this->serviceManager->get($form);
			$this->assertInstanceOf($form, $instance);
		}
	}
	
	//all forms SHOULD extend from base class Form
	/*public function test_ModelsExtendBaseClass()
	{
		for($i=0; $i<count($this->forms); $i++) {
			$form = $this->namespace.'\\'.$this->forms[$i];
			$this->assertInstanceOf($this->namespace.'\\'.$this->baseClass, $this->serviceManager->get($form));
		}
	}*/
}