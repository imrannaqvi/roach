<?php
namespace API\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;

class AuthenticationStorageFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		return new \API\Service\AuthenticationStorage($serviceLocator->get('Core\Model\User'));
	}
}