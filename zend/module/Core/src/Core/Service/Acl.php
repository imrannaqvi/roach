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
		
		foreach($this->config['resources'] as $resource => $permissions) {
			//merge common permissions
			$permissions = $this->config['resources'][$resource] = array_merge($this->config['common_permissions'], $permissions);
			//add resources
			$this->addResource($resource);
			for($i=0; $i<count($permissions); $i++) {
				$this->addResource(new Resource($resource.'.'.$permissions[$i]), $resource);
			}
		}
	}
}