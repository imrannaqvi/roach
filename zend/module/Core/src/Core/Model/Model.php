<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Adapter\Adapter;
use \Core\Service\LazyLoader;
use \Zend\ServiceManager\ServiceLocatorInterface;

class Model extends TableGateway
{
	public $table;
	protected $tables = array();
	protected $idColumn = 'id';
	protected $aclLazyLoader;
	protected $acl;
	protected $serviceLocator;
	
	public function __construct(
		Adapter $adapter,
		LazyLoader\Acl $aclLazyLoader,
		ServiceLocatorInterface $serviceLocator
	) {
		parent::__construct($this->table, $adapter);
		$this->aclLazyLoader = $aclLazyLoader;
		$this->serviceLocator = $serviceLocator;
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
	
	public function fetchAll($where = null)
	{
		$rs = $this->select($where);
		return $rs->toArray();
	}
	
	public function fetchOneById($id)
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
	
	/**
	 * __get
	 *
	 * @param  string $property
	 * 
	 * @return mixed
	 */
	public function __get($property)
	{
		//check if property exists as a model
		if(array_key_exists($property, (array) $this->tables)) {
			return $this->serviceLocator->get(__NAMESPACE__.'\\'.$this->tables[$property]);
		}
		//defaults to parent
		return parent::__get($property);
	}
}