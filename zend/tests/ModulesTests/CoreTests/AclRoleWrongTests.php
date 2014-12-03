<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class AclRoleWrongTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $acl;
	
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
		$this->acl = $this->serviceManager->get('Core\Service\Acl');
	}
	
	/**
	* @expectedException	Core\Service\AclException
	* @expectedExceptionCode	10
	*/
	public function test_UserWithNoRole()
	{
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => null,
			'role_id' => null
		));
	}
	
	/**
	* @expectedException	Core\Service\AclException
	* @expectedExceptionCode	20
	*/
	public function test_UserWithWrongDefaultRole()
	{
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => 'John-Doe',
			'role_id' => null
		));
	}
	
	/**
	* @expectedException	Core\Service\AclException
	* @expectedExceptionCode	30
	*/
	public function test_UserWithWrongRoleId()
	{
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => null,
			'role_id' => 1234
		));
	}
	
	/**
	* @expectedException	Core\Service\AclException
	* @expectedExceptionCode	20
	*/
	public function test_UserWithRoleIdWithWrongDefaultRole()
	{
		$model_role = $this->serviceManager->get('Core\Model\Role');
		//create a role for testing
		$uid = md5(time());
		$role_id = $model_role->insert(array(
			'name' => 'testing.role.'.$uid,
			//'parent_id' => 1,
			'parent_default_role' => 'this.wrong.default.role'
		));
		//check if role added
		$this->assertTrue((boolean) $role_id);
		//set user
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => null,
			'role_id' => $role_id
		));
	}
}
