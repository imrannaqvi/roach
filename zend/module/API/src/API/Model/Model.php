<?php
namespace API\Model;

use Zend\Authentication\AuthenticationService;

class Model
{
	protected $authentication;
	protected $acl;
	
	public function __construct(AuthenticationService $authentication, \Core\Service\Acl $acl)
	{
		$this->authentication = $authentication;
		$this->acl = $acl;
	}
}