<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Adapter\Adapter;
use \Core\Service\Acl;

class Model extends TableGateway
{
	public $table;
	protected $acl;
	
	public function __construct(Adapter $adapter, Acl $acl)
	{
		parent::__construct($this->table, $adapter);
		$this->acl = $acl;
	}
	
	public function insert($data)
	{
		if( parent::insert($data)) {
			return (int) $this->getLastInsertValue();
		}
		return null;
	}
}