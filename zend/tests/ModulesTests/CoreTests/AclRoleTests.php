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
	* @expectedExceptionCode	10
	*/
	public function test_UserWithNoRole()
	{
		$this->acl->setUser((object) array(
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
			'default_role' => null,
			'role_id' => 1234
		));
	}
}
