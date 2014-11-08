<?php
namespace Core\Service\LazyLoader;

use Zend\ServiceManager\ServiceLocatorInterface;

class Acl
{
	protected $serviceLocator;
	
	public function __construct(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function get()
	{
		return $this->serviceLocator->get('Core\Service\Acl');
	}
}