<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Adapter\Adapter;
use \Core\Service\LazyLoader;

class Model extends TableGateway
{
	public $table;
	protected $idColumn = 'id';
	protected $aclLazyLoader;
	protected $acl;
	
	public function __construct(Adapter $adapter, LazyLoader\Acl $aclLazyLoader)
	{
		parent::__construct($this->table, $adapter);
		$this->aclLazyLoader = $aclLazyLoader;
	}
	
	public function insert($data)
	{
		if(! $this->acl) {
			$this->acl = $this->aclLazyLoader->get();
		}
		if( parent::insert($data)) {
			return (int) $this->getLastInsertValue();
		}
		return null;
	}
	
	public function fetchOne($where = null)
	{
		$select = $this->sql->select();
		$select->limit(1);
		$rs = $this->select($where);
		if($rs && $rs->count()) {
			return $rs->current();
		}
		return null;
	}
	
	public function fetchById($id)
	{
		return $this->fetchOne(array(
			$this->idColumn => $id
		));
	}
	
	public function deleteById($id)
	{
		return $this->delete(array(
			$this->idColumn => $id
		));
	}
}