<?php
namespace Core\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Core\Model;

class Acl extends \Zend\Permissions\Acl\Acl
{
	private $config = array();
	private $user = null;
	
	public function __construct($config, Model\User $model_user, Model\Role $model_role)
	{
		$this->config = $config;
		$this->initConfig();
		$this->registerResources();
	}
	
	protected function initConfig()
	{
		//add resources and permissions as child resources
		foreach($this->config['resources'] as $resource => $permissions) {
			//merge common permissions
			$this->config['resources'][$resource] = array_merge(
				$this->config['common_permissions'],
				$permissions
			);
		}
	}
	
	protected function registerResources()
	{
		foreach($this->config['resources'] as $resource => $permissions) {
			$permissions = $this->config['resources'][$resource];
			//add resources
			$this->addResource($resource);
			for($i=0; $i<count($permissions); $i++) {
				$this->addResource(new Resource($resource.'.'.$permissions[$i]), $resource);
			}
		}
	}
	
	public function registerDefaultRoles()
	{
		//default roles
		$roles = array_key_exists('roles', $this->config) ? $this->config['roles'] :  array();
		//print_r($roles);
		foreach($roles as $key => $value) {
			if(! $this->hasRole($key)) {
				$this->addDefaultRoleFromConfig($key, $value);
			}
		}
	}
	
	protected function addDefaultRoleFromConfig($key, $details = null)
	{
		//get details if not passed
		if(
			!$details && 
			array_key_exists('roles', (array) $this->config) &&
			array_key_exists($key, (array) $this->config['roles'])
		) {
			$details = (array) $this->config['roles'][$key];
		}
		//check if extends form other roles
		$extends = array_key_exists('extends', (array) $details) ? $details['extends'] : null;
		//create and add role in acl
		$role = new Role($key);
		if($extends && !$this->hasRole($extends)) {
			$this->addDefaultRoleFromConfig($extends);
		}
		$this->addRole($role, $extends);
		//allow or deny all if
		if (
			array_key_exists('*', (array) $details) &&
			in_array($details['*'], array('allow', 'deny'))
		) {
			$this->$details['*']($role, null);
		}
		//permissions
		if(array_key_exists('permissions', (array) $details)) {
			foreach((array) $details['permissions'] as $resource => $permissions ) {
				//allow
				if(array_key_exists('allow', (array) $permissions)) {
					if($permissions['allow'] === true) {
						$this->allow($role, $resource);
					} else {
						$this->allow($role, array_map(function($s) use($resource) {
							return $resource.'.'.$s;
						}, (array) $permissions['allow']));
					}
				}
				//deny
				if(array_key_exists('deny', (array) $permissions)) {
					if($permissions['deny'] === true) {
						$this->deny($role, $resource);
					} else {
						$this->deny($role, array_map(function($s) use($resource) {
							return $resource.'.'.$s;
						}, (array) $permissions['deny']));
					}
				}
			}
		}
	}
	
	function setUser($user)
	{
		$this->user = $user;
		$this->loadUserRoleWithPermissions();
	}
	
	protected function loadUserRoleWithPermissions()
	{
		if($this->user->default_role && !$this->user->role_id) {
			if(! $this->hasRole($this->user->default_role)) {
				$this->addDefaultRoleFromConfig($this->user->default_role);
			}
		} elseif( $this->user->role_id ) {
			$this->addRoleFromModel($this->user->role_id);
		} else {
			throw new \Exception('role not defined for user.');
		}
	}
	
	protected function addRoleFromModel($role_id)
	{
		
	}
	
	public function getConfig()
	{
		return $this->config;
	}
}