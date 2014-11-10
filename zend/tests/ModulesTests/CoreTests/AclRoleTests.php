<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class AclRoleTests extends PHPUnit_Framework_TestCase
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
	*/
	public function test_UserWithWrongDefaultRole()
	{
		$this->acl->setUser((object) array(
			'default_role' => 'John-Doe',
			'role_id' => null
		));
	}
}