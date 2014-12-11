<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Adapter\Adapter;
use \Core\Service\LazyLoader;
use \Zend\ServiceManager\ServiceLocatorInterface;

class Model extends TableGateway
{
	/** @var string Table name. */
	public $table;
	
	/** @var array Associative array of property as key and value as another table. */
	protected $tables = array();
	
	/** @var array Primary key column name. */
	protected $idColumn = 'id';
	
	/** @var Core\Service\LazyLoader\Acl Lazy Loader class for Used for Core\Service\Acl. */
	protected $aclLazyLoader;
	
	/** @var Core\Service\Acl */
	protected $acl;
	
	/** @var Zend\ServiceManager\ServiceLocatorInterface For loading other tables. */
	protected $serviceLocator;
	
	/**
	 * Constructor
	 *
	 * @param Zend\Db\Adapter\Adapter $adapter
	 * @param Core\Service\LazyLoader\Acl $aclLazyLoader
	 * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 */
	public function __construct(
		Adapter $adapter,
		LazyLoader\Acl $aclLazyLoader,
		ServiceLocatorInterface $serviceLocator
	) {
		parent::__construct($this->table, $adapter);
		$this->aclLazyLoader = $aclLazyLoader;
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @param array $data.
	 * @return integer|null
	 */
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
	
	/**
	 * @param array $where.
	 * @return array|null
	 */
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
	
	/**
	 * @param array $where.
	 * @return array
	 */
	public function fetchAll($where = null)
	{
		$rs = $this->select($where);
		return $rs->toArray();
	}
	
	/**
	 * @param integer $id.
	 * @return array|null
	 */
	public function fetchOneById($id)
	{
		return $this->fetchOne(array(
			$this->idColumn => $id
		));
	}
	
	/**
	 * @param int $id.
	 * @return int
	 */
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