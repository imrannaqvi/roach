<?php
namespace Core\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		return new \Core\Service\Acl(
			include './config/acl.config.php',
			$serviceLocator->get('Core\Model\User'),
			$serviceLocator->get('Core\Model\Role'),
			$serviceLocator->get('Core\Model\RolePermission')
		);
	}
}