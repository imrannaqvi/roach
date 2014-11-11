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
	
	public function test_WrongResource()
	{
		$this->acl->registerDefaultRoles();
		$roles = $this->acl->getRoles();
		if(count($roles)) {
			try{
				$this->acl->isAllowed($roles[0], 'this.should.not.be.a.resource');
			} catch(InvalidArgumentException $e) {
				$this->assertTrue(true);
				return;
			}
			$this->assertTrue(false);
		}
	}
}