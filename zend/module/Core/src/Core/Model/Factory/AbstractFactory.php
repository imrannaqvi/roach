<?php
namespace Core\Model\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractFactory implements AbstractFactoryInterface
{
	public function canCreateServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return strpos($requestedName, 'Core\\Model\\') === 0 && class_exists($requestedName) ? true : false;
	}
	
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return new $requestedName($serviceLocator->get('Zend\Db\Adapter\Adapter'));
	}
}