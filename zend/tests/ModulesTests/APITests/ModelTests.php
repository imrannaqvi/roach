<?php
namespace ModulesTests\APITests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class ModelTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $namespace = 'API\Model';
	protected $baseClass = 'Model';
	protected $models = array(
		'Index'
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
		//Model base class SHOULD NOT BE accessible as service
		$this->serviceManager->get($this->namespace.'\\'.$this->baseClass);
	}
	
	//all models SHOULD BE accessible as a service
	public function test_ModelsAsService()
	{
		for($i=0; $i<count($this->models); $i++) {
			$model = $this->namespace.'\\'.$this->models[$i];
			$instance = $this->serviceManager->get($model);
			$this->assertInstanceOf($model, $instance);
		}
	}
	
	//all models SHOULD extend from base class Model
	public function test_ModelsExtendBaseClass()
	{
		for($i=0; $i<count($this->models); $i++) {
			$model = $this->namespace.'\\'.$this->models[$i];
			$this->assertInstanceOf($this->namespace.'\\'.$this->baseClass, $this->serviceManager->get($model));
		}
	}
}