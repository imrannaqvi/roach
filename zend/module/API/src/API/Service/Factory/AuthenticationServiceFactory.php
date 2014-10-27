<?php
namespace API\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class AuthenticationServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
		$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'username', 'password', 'MD5(?) AND status = "active"');
		return new AuthenticationService(
			$serviceLocator->get('API\Service\AuthenticationStorage'),
			$dbTableAuthAdapter
		);
	}
}