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
	}
}