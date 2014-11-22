<?php
namespace ModulesTests\APITests\Model;

use \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AccessTests extends AbstractHttpControllerTestCase
{
	public function setUp()
	{
		$this->setApplicationConfig(
			include __DIR__ . '/../../../config/application.config.php'
		);
		parent::setUp();
	}
	
	public function testIndexActionCanBeAccessed()
	{
		$this->dispatch('/');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('api');
		$this->assertControllerName('api\controller\index');
		$this->assertControllerClass('IndexController');
		$this->assertMatchedRouteName('api');
		$this->assertResponseHeaderContains('content-type', 'application/json; charset=utf-8');
		// get response
		$response = json_decode($this->getResponse()->getBody());
		//check if response is json
		$this->assertTrue(!! $response, 'Response is expected as JSON.');
		$response = (array) $response;
		$this->assertArrayHasKey('method', $response);
		$this->assertEquals('', $response['method']);
		$this->assertArrayHasKey('error', $response);
		$this->assertEquals('method-not-found', $response['error']);
		$this->assertArrayHasKey('response', $response);
		$this->assertNull($response['response']);
	}
}