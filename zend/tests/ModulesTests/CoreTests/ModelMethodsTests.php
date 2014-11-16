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
	
	public function test_fetchAllDeleteById()
	{
		$user_model = $this->serviceManager->get('Core\Model\User');
		//fetch zero number of records
		$all = $user_model->fetchAll(array(
			'token' => 'this.should.not.be.a.valid.token'
		));
		$this->assertInternalType('array', $all);
		$this->assertEquals(0, count($all));
		## fetch non-zero number of records
		//1 - create user records
		$ids = array();
		for($i=0; $i<3; $i++) {
			$uid = md5(time().rand());
			$id = $user_model->insert(array(
				'username' => $uid,
				'password' =>  md5($uid),
				'email' => $uid.'@yahoo.com',
				'status' => 'active'
			));
			$ids[] = $id;
			//test if user was created
			$this->assertTrue((boolean) $id);
		}
		//fetchAll should return count($ids) records
		$all = $user_model->fetchAll(array(
			'id' => $ids
		));
		$this->assertEquals(count($ids), count($all));
		//deleteById and fetchAll, should return zero records
		$user_model->deleteById($ids);
		$all = $user_model->fetchAll(array(
			'id' => $ids
		));
		$this->assertEquals(0, count($all));
	}
}