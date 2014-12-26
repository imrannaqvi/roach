<?php
namespace API\Model\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractFactory implements AbstractFactoryInterface
{
	public function canCreateServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return $requestedName !== 'API\Model\Model' &&
			strpos($requestedName, 'API\\Model\\') === 0 &&
			class_exists($requestedName)
		? true : false;
	}
	
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return new $requestedName(
			$serviceLocator->get('API\Service\AuthenticationService'),
			$serviceLocator->get('Core\Service\Acl')
		);
	}
}