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
	
	public function read($header = false)
	{
		if($header && get_class($header) === 'Zend\Http\Header\Authorization') {
			$rd = $this->table->select(array(
				'token' => substr($header->getFieldValue(), 6)
			));
			if($rd && $rd->count()) {
				$this->contents = $rd->current();
			}
		}
		return $this->contents;
	}
	
	public function write($contents)
	{
		if(gettype($contents) === 'object') {
			do {
				$md5 = md5(time().rand());
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
		if($this->contents && $this->contents->id){
			$this->table->update(array(
				'token' => md5(time().rand())
			), array(
				'id' => $this->contents->id
			));
		}
		$this->contents = null;
	}
}