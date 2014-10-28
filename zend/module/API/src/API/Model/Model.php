<?php
namespace API\Model;

use Zend\Authentication\AuthenticationService;

class Model
{
	protected $authentication;
	
	public function __construct(AuthenticationService $authentication)
	{
		$this->authentication = $authentication;
	}
}