<?php
namespace Core\Model;

use \Zend\Db\TableGateway\TableGateway;
use \Zend\Db\Adapter\Adapter;
use \Core\Service\LazyLoader;

class Model extends TableGateway
{
	public $table;
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
}