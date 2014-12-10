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
		
	//all models SHOULD extend from base class Zend\Db\TableGateway\TableGateway
	public function test_ModelsExtendZendDbTableGateway()
	{
		for($i=0; $i<count($this->models); $i++) {
			$model = $this->namespace.'\\'.$this->models[$i];
			$this->assertInstanceOf('Zend\Db\TableGateway\TableGateway', $this->serviceManager->get($model));
		}
	}
	
	//all models SHOULD BE able to access db, correct talbe name and id(PK) is tested
	public function test_ModelsCanAccessDb()
	{
		for($i=0; $i<count($this->models); $i++) {
			$model = $this->namespace.'\\'.$this->models[$i];
			$instance = $this->serviceManager->get($model);
			$this->assertTrue(!! $instance->select(array('id' => 1)));
		}
	}
	
	public function test_UserModel()
	{
		$model_user = $this->serviceManager->get('Core\Model\User');
		$this->assertInstanceOf('Core\Model\UserPermission', $model_user->model_userPermission);
	}
}