<?php
namespace API\Service;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Session\Config\SessionConfig;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;

class AuthenticationStorage implements StorageInterface
{
	protected $contents = null;
	protected $table = null;
	
	function __construct(\Core\Model\User $table)
	{
		$this->table = $table;
	}
	
	public function read()
	{
		return $this->contents;
	}
	
	public function write($contents)
	{
		if(gettype($contents) === 'object') {
			$md5 = md5(microtime(true));
			do {
				$rd = $this->table->select(array(
					'token' => $md5
				));
			} while ($rd->count());
			$toc = date('Y-m-d H:i:s');
			if( $this->table->update(array(
				'token' => $md5,
				'token_toc' => $toc
			), array(
				'id' => $contents->id
			))) {
				$contents->token = $md5;
				$contents->token_toc = $toc;
			}
			$this->contents = $contents;
			return $this->contents;
		}
	}
	
	public function isEmpty()
	{
		return !! $this->contents;
	}
	
	public function clear()
	{
		$this->contents = null;
	}
}