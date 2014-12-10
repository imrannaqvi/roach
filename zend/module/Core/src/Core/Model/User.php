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
		//TODO: add implementation
		return array();
	}
}