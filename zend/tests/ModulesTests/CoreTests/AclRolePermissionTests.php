<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class AclRolePermissionTests extends PHPUnit_Framework_TestCase
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
	* @expectedExceptionCode	110
	*/
	public function test_AclWithNoActiveUser()
	{
		$this->acl->isAllowedToActiveRole('some.resource');
	}
	
	public function test_UserWithRoleIdWithCorrectDefaultRole()
	{
		$model_role = $this->serviceManager->get('Core\Model\Role');
		$model_rolePermission = $this->serviceManager->get('Core\Model\RolePermission');
		//get all roles and clear acl from roles
		$this->acl->registerDefaultRoles();
		$roles = $this->acl->getRoles();
		$this->acl->removeRoleAll();
		## create a role for testing
		$uid = md5(time().rand());
		$prole = end($roles);
		$role_id = $model_role->insert(array(
			'name' => 'testing.role.'.$uid,
			'parent_default_role' => $prole
		));
		// check if role added
		$this->assertTrue((boolean) $role_id);
		// set user
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => null,
			'role_id' => $role_id
		));
		// permissions
		$permissions = $this->acl->getResources();
		$ppermissions = array();
		for($i=0; $i<count($permissions); $i++) {
			if(strpos($permissions[$i], '.') === false) {
				$ppermissions[$permissions[$i]] = $this->acl->isAllowedToActiveRole($permissions[$i]);
			}
		}
		## create another role for testing
		$uid = md5(time().rand());
		$role_id2 = $model_role->insert(array(
			'name' => 'testing.role.'.$uid,
			'parent_default_role' => $prole
		));
		// check if role added
		$this->assertTrue((boolean) $role_id2);
		//add role permissions as inverse from previous role
		foreach($ppermissions as $key => $value) {
			$model_rolePermission->insert(array(
				'role_id' => $role_id2,
				'permission' => $key,
				'access' => $value ? 'deny' : 'allow'
			));
		}
		// clone first acl and set user
		$acl2 = clone($this->acl);
		$this->acl->setUser((object) array(
			'id' => 1,
			'default_role' => null,
			'role_id' => $role_id2
		));
		## compare both acls
		foreach($ppermissions as $key => $value) {
			$this->assertEquals(
				$this->acl->isAllowedToActiveRole($key),
				! $acl2->isAllowedToActiveRole($key)
			);
		}
		## check if role was deleted
		$this->assertEquals(2, $model_role->deleteById(array($role_id, $role_id2)));
	}
}
