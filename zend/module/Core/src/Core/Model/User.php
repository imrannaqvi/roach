<?php
namespace Core\Model;

class User extends Model
{
	public $table = 'user';
	
	public function getUserPermissions($user_id)
	{
		return array();
	}
}