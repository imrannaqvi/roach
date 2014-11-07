<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class AclTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	protected $acl;
	
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
		$this->acl = $this->serviceManager->get('Core\Service\Acl');
	}
	
	public function test_BaseClass()
	{
		$this->assertInstanceOf('Core\Service\Acl', $this->acl);
		$this->assertInstanceOf('Zend\Permissions\Acl\Acl', $this->acl);
	}
	
	public function test_Resourses()
	{
		$config = $this->acl->getConfig();
		foreach($config['resources'] as $resource => $permissions) {
			//has resources
			$this->assertTrue($this->acl->hasResource($resource));
			for($i=0; $i<count($permissions); $i++) {
				$this->assertTrue($this->acl->hasResource($resource.'.'.$permissions[$i]));
			}
		}
	}
	
	public function test_Roles()
	{
		$config = $this->acl->getConfig();
		$this->acl->registerDefaultRoles();
		//default roles
		$roles = array_key_exists('roles', $config) ? $config['roles'] :  array();
		foreach($roles as $key => $value) {
			//check if roles exists
			$this->assertTrue($this->acl->hasRole($key));
			//check if it inherits from another role
			if(array_key_exists('extends', $value)) {
				$this->assertTrue($this->acl->inheritsRole($key, $value['extends']));
			}
		}
	}
}