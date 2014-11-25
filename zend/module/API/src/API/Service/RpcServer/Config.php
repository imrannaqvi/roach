<?php
namespace API\Service\RpcServer;

class Config
{
	private $config = array();
	public $authenticationRequired = false;
	
	function __construct($config)
	{
		$this->config = $config;
		if(
			array_key_exists('authentication_required', $this->config) &&
			$this->config['authentication_required']
		) {
			$this->authenticationRequired = true;
		}
	}
	
	public function methodExists($method)
	{
		if(
			array_key_exists('methods', (array) $this->config) &&
			array_key_exists($method, (array) $this->config['methods'])
		) {
			return true;
		}
		return false;
	}
	
	private function prepareMethodDetails($item)
	{
		if(! array_key_exists('authentication_required', $item)) {
			$item['authentication_required'] = $this->authenticationRequired;
		}
		return $item;
	}
	
	public function getMethodDetails($method)
	{
		if(! $this->methodExists($method)) {
			return null;
		}
		
		return $this->prepareMethodDetails($this->config['methods'][$method]);
	} 
}