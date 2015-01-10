<?php
namespace API;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getServiceConfig()
	{
		return array(
			'abstract_factories' => array(
				'API\Model\Factory\AbstractFactory',
				'API\Form\Factory\AbstractFactory'
			),
			'factories' => array(
				'API\Service\AuthenticationService' => 'API\Service\Factory\AuthenticationServiceFactory',
				'API\Service\AuthenticationStorage' => 'API\Service\Factory\AuthenticationStorageFactory'
			)
		);
	}
}