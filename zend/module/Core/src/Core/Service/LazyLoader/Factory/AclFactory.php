<?php
namespace Core\Service\LazyLoader\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		return new \Core\Service\LazyLoader\Acl($serviceLocator);
	}
}