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
	
	public function test_fetchOnefetchById()
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
		//fetchById
		$user = $user_model->fetchById($id);
		$this->assertEquals($id, $user->id);
		$user = $user_model->fetchById($id + 1);
		$this->assertNull($user);
		//delete the user created for testing
		$this->assertTrue((boolean) $user_model->delete(array(
			'id' => $id
		)));
	}
}