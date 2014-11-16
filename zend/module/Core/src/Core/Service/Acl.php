<?php
namespace Core\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Core\Model;

class Acl extends \Zend\Permissions\Acl\Acl
{
	private $config = array();
	private $user = null;
	private $current_role;
	private $model_user;
	private $model_role;
	
	
	const ERRCODE_ROLE_NOT_SET_FOR_USER = 10;
	const ERRCODE_DEFUAL_ROLE_NOT_FOUND = 20;
	const ERRCODE_ROLE_NOT_FOUND_IN_MODEL = 30;
	
	public function __construct($config, Model\User $model_user, Model\Role $model_role)
	{
		$this->config = $config;
		$this->model_user = $model_user;
		$this->model_role = $model_role;
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
			$this->addDefaultRoleFromConfig($key, $value);
		}
	}
	
		
	function setUser($user)
	{
		$this->user = $user;
		$this->loadUserRoleWithPermissions();
	}
	
	protected function loadUserRoleWithPermissions()
	{
		//if role from model
		if( $this->user->role_id ) {
			$this->current_role = $this->user->role_id;
			return $this->addRoleFromModel($this->user->role_id);
		}
		//if default role
		if($this->user->default_role) {
			$this->current_role = $this->user->default_role;
			return $this->addDefaultRoleFromConfig($this->user->default_role);
		}
		throw new AclException('role not defined for user.', self::ERRCODE_ROLE_NOT_SET_FOR_USER);
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
		if(! $details) {
			throw new AclException('"'.$key.'" Role not found in config.', self::ERRCODE_DEFUAL_ROLE_NOT_FOUND);
		}
		//return if role already exists
		if($this->hasRole($key)) {
			return;
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
	
	protected function addRoleFromModel($role_id)
	{
		$role = $this->model_role->fetchOneById($role_id);
		if(! $role) {
			throw new AclException("role '$role_id' not found in model.", self::ERRCODE_ROLE_NOT_FOUND_IN_MODEL);
		}
		//return if role already exists
		if($this->hasRole($role_id)) {
			return;
		}
		//check if extends form other roles
		$extends = $role->parent_id ? $role->parent_id : ( 
			$role->parent_default_role ? $role->parent_default_role : null
		);
		//create and add role in acl
		$role = new Role($role_id);
		if($extends && !$this->hasRole($extends)) {
			if((int) $extends) {
				$this->addRoleFromModel($extends);
			} else {
				$this->addDefaultRoleFromConfig($extends);
			}
		}
		$this->addRole($role, $extends);
		//TODO: add permissions to role
	}
	
	public function getConfig()
	{
		return $this->config;
	}
}