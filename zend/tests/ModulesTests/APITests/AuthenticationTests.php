<?php
namespace ModulesTests\APITests\Model;

use \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use \Zend\Stdlib\Parameters;

class AuthenticationTests extends AbstractHttpControllerTestCase
{
	public function setUp()
	{
		$this->setApplicationConfig(
			include __DIR__ . '/../../../config/application.config.php'
		);
		parent::setUp();
	}
	
	public function testLogin()
	{
		$user_model = $this->getApplicationServiceLocator()->get('Core\Model\User');
		$authentication = $this->getApplicationServiceLocator()->get('API\Service\AuthenticationService');
		//create user record
		$uid = md5(time());
		$id = false;
		if( $user_model->insert(array(
			'username' => $uid,
			'password' =>  md5($uid),
			'email' => $uid.'@yahoo.com',
			'status' => 'active'
		))) {
			$id = (int) $user_model->getLastInsertValue();
		}
		//test if user was created
		$this->assertTrue((boolean) $id, 'User not created for authentication tests.');
		//send login request
		$response = $this->api('login', array(
			'username' => $uid,
			'password' => $uid
		));
		//test if login was successful
		$this->assertArrayHasKey('error', $response, 'API response do not returned "error" key.');
		$this->assertFalse($response['error'], '"error" key should be false.');
		$this->assertArrayHasKey('response', $response, 'API response do not returned "response" key.');
		$token = (array) $response['response'];
		$this->assertArrayHasKey('token', $token, '"token" not returned as a key.');
		$token = $token['token'];
		$this->assertEquals( 32, strlen($token), 'Not a proper MD5 token.');
		//get storage and test it with login request data
		$storage = $authentication->getStorage()->read();
		$this->assertEquals($id, $storage->id);
		$this->assertEquals($uid, (string) $storage->username);
		$this->assertEquals($token, (string) $storage->token);
		$this->assertEquals('active', (string) $storage->status);
		//delete the user created for testing
		$this->assertTrue((boolean) $user_model->delete(array(
			'id' => $id
		)), 'Testing user was not deleted.');
	}
	
	protected function api($method = '', $params = array())
	{
		$this->dispatch('/', 'POST', array(
			'method' => $method,
			'params' => $params
		));
		return (array) json_decode($this->getResponse()->getBody());
	}
}