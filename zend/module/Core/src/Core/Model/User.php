<?php
namespace Core\Model;

class User extends Model
{
	public $table = 'user';
	protected $tables = array(
		'model_userPermission' => 'UserPermission'
	);
	
	public function getUserPermissions($user_id)
	{
		return $this->model_userPermission->fetchAll(array(
			'user_id' => $user_id
		));
	}
}