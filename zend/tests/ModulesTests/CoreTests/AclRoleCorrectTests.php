<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class AclRoleCorrectTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $acl;
	
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
		$this->acl = $this->serviceManager->get('Core\Service\Acl');
		$this->acl->registerDefaultRoles();
		$this->default_roles = $this->acl->getRoles();
	}
	
	public function test_UserWithRoleIdWithCorrectDefaultRole()
	{
		$model_role = $this->serviceManager->get('Core\Model\Role');
		//get all roles and clear acl from roles
		$this->acl->registerDefaultRoles();
		$roles = $this->acl->getRoles();
		//print_r($roles);
		$this->acl->removeRoleAll();
		//create a role for testing
		$uid = md5(time());
		$prole = end($roles);
		$role_id = $model_role->insert(array(
			'name' => 'testing.role.'.$uid,
			//'parent_id' => 1,
			'parent_default_role' => $prole
		));
		//check if role added
		$this->assertTrue((boolean) $role_id);
		//set user
		$this->acl->setUser((object) array(
			'default_role' => null,
			'role_id' => $role_id
		));
		//check if newly created role registered
		$this->assertContains((string) $role_id, $this->acl->getRoles());
		//check if parent role was registered
		$this->assertContains((string) $prole, $this->acl->getRoles());
		//check if role was deleted
		$this->assertTrue((boolean) $model_role->deleteById($role_id));
	}
}
