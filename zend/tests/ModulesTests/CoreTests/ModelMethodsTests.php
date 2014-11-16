<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class ModelMethodsTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function test_fetchOnefetchOneByIdDeleteById()
	{
		$user_model = $this->serviceManager->get('Core\Model\User');
		//1 - create user record
		$uid = md5(time());
		$id = $user_model->insert(array(
			'username' => $uid,
			'password' =>  md5($uid),
			'email' => $uid.'@yahoo.com',
			'status' => 'active'
		));
		//test if user was created
		$this->assertTrue((boolean) $id);
		//fetchOne
		$user = $user_model->fetchOne(array('id' => $id));
		$this->assertEquals($id, $user->id);
		$user = $user_model->fetchOne(array('id' => $id + 1));
		$this->assertNull($user);
		//fetchOneById
		$user = $user_model->fetchOneById($id);
		$this->assertEquals($id, $user->id);
		$user = $user_model->fetchOneById($id + 1);
		$this->assertNull($user);
		//deleteById the user created for testing
		$this->assertTrue((boolean) $user_model->deleteById($id));
		//now it should not be there
		$this->assertNull($user_model->fetchOneById($id));
	}
	
	
}