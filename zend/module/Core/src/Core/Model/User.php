<?php
namespace Core\Model;

class User extends Model
{
	public $table = 'user';
	
	public function getUserPermissions($user_id)
	{
		//TODO: add implementation, probably mechanisam to access other models within current model
		return array();
	}
}