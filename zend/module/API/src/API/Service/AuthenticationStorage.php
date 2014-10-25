<?php
namespace API\Service;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Session\Config\SessionConfig;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;

class AuthenticationStorage implements StorageInterface
{
	public function __construct()
	{
	}
	
	public function read()
	{
	}
	
	public function write($contents)
	{
	}
	
	public function isEmpty()
	{
	}
	
	public function clear()
	{
	}
}