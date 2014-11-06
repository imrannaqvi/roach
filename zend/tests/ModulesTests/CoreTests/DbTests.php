<?php
namespace ModulesTests\CoreTests\Model;

use PHPUnit_Framework_TestCase;
use ModulesTests\ServiceManagerGrabber;

class DbTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;
	
	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function test_TocTolu()
	{
		$user_model = $this->serviceManager->get('Core\Model\User');
		$uid = md5(time());
		$data = array(
			'username' => $uid,
			'password' =>  md5($uid),
			'email' => $uid.'@yahoo.com',
			'status' => 'active'
		);
		//test non of toc and tolu was provided
		$this->assertFalse(array_key_exists('toc', $data));
		$this->assertFalse(array_key_exists('tolu', $data));
		//1 - create user record
		$id = $user_model->insert($data);
		//test if user was created
		$this->assertTrue((boolean) $id);
		//get the record
		$rs = $user_model->select(array('id' => $id));
		$rd = $rs->current();
		$this->assertNotEmpty($rd->toc);
		$this->assertNotEmpty($rd->tolu);
		$this->assertEquals($rd->toc, $rd->tolu);
		//echo($rd['id']);
		//print_r($rd->getArrayCopy());
		//sleep
		sleep(1);
		//prepare data for update
		$data = array(
			'avatar' => '123'
		);
		//test non of toc and tolu was provided
		$this->assertFalse(array_key_exists('toc', $data));
		$this->assertFalse(array_key_exists('tolu', $data));
		//update
		$user_model->update($data, array('id' => $id));
		//get the record
		$rs = $user_model->select(array('id' => $id));
		$rd = $rs->current();
		//echo($rd['id']);
		//print_r($rd->getArrayCopy());
		$this->assertNotEmpty($rd->toc);
		$this->assertNotEmpty($rd->tolu);
		$this->assertNotEquals($rd->toc, $rd->tolu);
		//delete the user created for testing
		$this->assertTrue((boolean) $user_model->delete(array(
			'id' => $id
		)), 'Testing user was not deleted.');
	}
}