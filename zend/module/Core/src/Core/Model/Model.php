<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Model extends TableGateway
{
	public $table;
	
	public function __construct(Adapter $adapter)
	{
		parent::__construct($this->table, $adapter);
	}
}