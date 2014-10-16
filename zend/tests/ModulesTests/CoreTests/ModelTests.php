<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class ModelTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $namespace = 'Core\Model';
	protected $baseClass = 'Model';
	protected $models = array(
		'Attachment',
		'Comment',
		'Organisation',
		'OrganisationUser',
		'OrganisationUserPermission',
		'Project',
		'ProjectUser',
		'ProjectUserPermission',
		'Role',
		'RolePermission',
		'Task',
		'TaskStatusHistory',
		'User',
		'UserPermission',
		'Workflow',
		'WorkflowAction',
		'WorkflowStatus'
	);
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function testModelsAsAService()
	{
		//all models SHOULD BE accessible as a service
		for($i=0; $i<count($this->models); $i++) {
			$this->assertInstanceOf($this->namespace.'\\'.$this->models[$i], $this->serviceManager->get($this->namespace.'\\'.$this->models[$i]));
		}
	}
	
	/**
	* @expectedException	Zend\ServiceManager\Exception\ServiceNotFoundException
	*/
	public function testBaseClassAsAService()
	{
		//Model base class SHOULD NOT BE accessible as service
		$this->serviceManager->get($this->namespace.'\\'.$this->baseClass);
	}
}