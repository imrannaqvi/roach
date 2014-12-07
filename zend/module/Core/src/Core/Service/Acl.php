<?php
namespace Core\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Core\Model;

class Acl extends \Zend\Permissions\Acl\Acl
{
	/** @var array Used for storing config for acl. */
	private $config = array();
	
	/** @var null|object Current logged in user. */
	private $user = null;
	
	/** @var string Active acl role for current logged in user. */
	private $active_role;
	
	/** @var string Active acl level. */
	private $active_level;
	
	/** @var null|int Organisation id for current logged in user, set by setOrganisation method. */
	private $active_organisation = null;
	
	/** @var null|int Project id for current logged in user, set by setProject method. */
	private $active_project = null;
	
	/** @var Core\Model\User */
	private $model_user;
	
	/** @var Core\Model\Role */
	private $model_role;
	
	/** @var Core\Model\RolePermission */
	private $model_rolePermission;
	
	/** Exception type: No role was set for current user. */
	const ERRCODE_ROLE_NOT_SET_FOR_USER = 10;
	
	/** Exception type: Default role not found in config.roles. */
	const ERRCODE_DEFUAL_ROLE_NOT_FOUND = 20;
	
	/** Exception type: Role not found in Role Model. */
	const ERRCODE_ROLE_NOT_FOUND_IN_MODEL = 30;
	
	/** Exception type: User not set for acl. */
	const ERRCODE_USER_NOT_SET = 110;
	
	/** Pemission Level: global */
	const PERMISSION_LEVEL_GLOBAL = 'global';
	
	/** Pemission Level: organisation */
	const PERMISSION_LEVEL_ORGANISATION = 'organisation';
	
	/** Pemission Level: project */
	const PERMISSION_LEVEL_PROJECT = 'project';
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 * @param Core\Model\User $model_user
	 * @param Core\Model\Role $model_role
	 * @param Core\Model\RolePermission $model_rolePermission
	 */
	public function __construct(
		$config,
		Model\User $model_user,
		Model\Role $model_role,
		Model\RolePermission $model_rolePermission
	) {
		$this->config = $config;
		//models
		$this->model_user = $model_user;
		$this->model_role = $model_role;
		$this->model_rolePermission = $model_rolePermission;
		//initializations
		$this->initConfig();
		$this->registerResources();
	}
	
	/**
	 * Initialize required changes in loaded config.
	 */
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
	
	/**
	 * Register acl resources in current acl.
	 */
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
	
	/**
	 * Register all default roles (roles defined in config.roles) in current acl. Only used in tests.
	 */
	public function registerDefaultRoles()
	{
		//default roles
		$roles = array_key_exists('roles', $this->config) ? $this->config['roles'] :  array();
		//print_r($roles);
		foreach($roles as $key => $value) {
			$this->addDefaultRoleFromConfig($key, $value);
		}
	}
	
	/**
	 * Set user for current acl.
	 *
	 * @param object $user
	 */
	function setUser($user)
	{
		$this->active_level = self::PERMISSION_LEVEL_GLOBAL;
		$this->active_organisation = null;
		$this->active_project = null;
		$this->user = $user;
		$this->loadUserRoleWithPermissions();
		$this->loadUserPermissions();
		//TODO: load user projects and organisations
		
	}
	
	/**
	 * Load Role of current user to current acl. Role might be role from Role Model or from acl config.
	 *
	 * @throws Core\Service\AclException
	 */
	protected function loadUserRoleWithPermissions()
	{
		//if role from model
		if( $this->user->role_id ) {
			$this->active_role = $this->user->role_id;
			return $this->addRoleFromModel($this->user->role_id);
		}
		//if default role
		if($this->user->default_role) {
			$this->active_role = $this->user->default_role;
			return $this->addDefaultRoleFromConfig($this->user->default_role);
		}
		throw new AclException('role not defined for user.', self::ERRCODE_ROLE_NOT_SET_FOR_USER);
	}
	
	/**
	 * Add role in current acl defined in config.roles associative array.
	 *
	 * @param string $roleName A key from config.roles.
	 * @param null|array details for config.roles as array.
	 *
	 * @throws Core\Service\AclException
	 */
	protected function addDefaultRoleFromConfig($roleName, $details = null)
	{
		//get details if not passed
		if(
			!$details && 
			array_key_exists('roles', (array) $this->config) &&
			array_key_exists($roleName, (array) $this->config['roles'])
		) {
			$details = (array) $this->config['roles'][$roleName];
		}
		if(! $details) {
			throw new AclException('"'.$roleName.'" Role not found in config.', self::ERRCODE_DEFUAL_ROLE_NOT_FOUND);
		}
		//return if role already exists
		if($this->hasRole($roleName)) {
			return;
		}
		//check if extends form other roles
		$extends = array_key_exists('extends', (array) $details) ? $details['extends'] : null;
		//create and add role in acl
		$role = new Role($roleName);
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
	
	/**
	 * Add role in current acl defined in Roles Model.
	 *
	 * @param int $role_id Role id of Role.
	 *
	 * @throws Core\Service\AclException
	 */
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
		//add permissions to role
		$role_permissions = $this->model_rolePermission->fetchByRoleId($role_id);
		$this->addPermissionsToRole($role, $role_permissions);
	}
	
	/**
	 * Add user specific permissions for logged in user in current acl.
	 */
	protected function loadUserPermissions()
	{
		$permissions = $this->model_user->getUserPermissions($this->user->id);
		//TODO: add a new user specific rolr in acl and active_role to new role 
		$this->addPermissionsToRole($this->active_role, $permissions);
	}
	
	/**
	 * Helper method to add allow/deny specific permissions for a given user in current acl.
	 *
	 * @param string $role A valid role from acl.
	 * @param string $permissions A list of permissions and whether each one is allow/deny.
	 */
	protected function addPermissionsToRole($role, $permissions)
	{
		for($i=0; $i<count($permissions); $i++) {
			if($permissions[$i]['access'] == 'allow') {
				$this->allow($role, $permissions[$i]['permission']);
			} else {
				$this->deny($role, $permissions[$i]['permission']);
			}
		}
	}
	
	/**
	 * Set current acl level to specific Organisation.
	 * There may be many organisations current user is associated to. 
	 * This method uses different acl roles for per user per organisation for easy reloading and conflict resolution.  
	 *
	 * @param string $organisation_id Organisation id from Organisation Model.
	 */
	public function setOrganisation($organisation_id)
	{
		//TODO: check $project_id against user assigned organisations 
		$this->active_level = self::PERMISSION_LEVEL_ORGANISATION;
		$this->active_organisation = $organisation_id;
		/*
		//TODO:
		create a new role $active_role.'_org_'.$organisation_id
		inherited from $active_role
		add any organisation related permissions
		*/
	}
	
	/**
	 * Set current acl level to specific Project.
	 * There may be many projects current user is associated to. 
	 * This method uses different acl roles for per user per organisation per project for easy reloading and conflict resolution.  
	 *
	 * @param string $project_id Project id from Project Model.
	 */
	public function setProject($project_id)
	{
		//TODO: dependency: current role should be of 'organisation' level
		//TODO: check $project_id against user assigned projects 
		$this->active_level = self::PERMISSION_LEVEL_PROJECT;
		$this->active_project = $project_id;
		/*
		//TODO:
		create a new role $active_role.'_org_'.$organisation_id.'_proj_'.$project_id 
		inherited from $active_role.'_org_'.$organisation_id 
		add any project related permissions
		*/
	}
	
	/**
	 * To check whether a specific resource/permission is allowed to current logged in user.
	 *
	 * @param string|Zend\Permissions\Acl\Resource\ResourceInterface resource or permission.
	 *
	 * @throws Core\Service\AclException
	 * @return boolean
	 */
	public function isAllowedToActiveRole($resource)
	{
		if(! $this->active_role) {
			throw new AclException("Acl User not set.", self::ERRCODE_USER_NOT_SET);
		}
		return $this->isAllowed($this->active_role, $resource);
	}
	
	/**
	 * Get config.
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}
}