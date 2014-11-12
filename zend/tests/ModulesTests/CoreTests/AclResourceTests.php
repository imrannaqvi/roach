<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;

class AclResourceTests extends PHPUnit_Framework_TestCase
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
	* @expectedException	Zend\Permissions\Acl\Exception\InvalidArgumentException
	*/
	public function test_WrongResource()
	{
		$this->acl->getResource('this.should.not.be.a.resource');
	}
}