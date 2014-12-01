<?php
namespace Core\Model;

class RolePermission extends Model
{
	public $table = 'role_permission';
	
	public function fetchByRoleId($role_id)
	{
		return $this->fetchAll(array(
			'role_id' => $role_id
		));
	}
}