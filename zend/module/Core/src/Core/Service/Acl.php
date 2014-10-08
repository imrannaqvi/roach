<?php
namespace Core\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends \Zend\Permissions\Acl\Acl
{
	private $config = array();
	
	public function __construct($config)
	{
		$this->config = $config;
	}
}