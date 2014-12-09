<?php
namespace API\Service\RpcServer;

class Config
{
	/** @var array Used for storing config array. */
	private $config = array();
	
	/** @var boolean Whether authentication is required for all method calls. */
	public $authenticationRequired = false;
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
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
	
	/**
	 * To check if method exists in config or not.
	 *
	 * @param string $method Method name.
	 *
	 * @return boolean
	 */
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
	
	/**
	 * Prepare a single method details before dispatch.
	 *
	 * @param array $item Settings item of a single method from config.
	 *
	 * @return array
	 */
	private function prepareMethodDetails($item)
	{
		if(! array_key_exists('authentication_required', $item)) {
			$item['authentication_required'] = $this->authenticationRequired;
		}
		return $item;
	}
	
	/**
	 * Get config details for a single method.
	 *
	 * @param string $method Settings item of a single method from config.
	 *
	 * @return array
	 */
	public function getMethodDetails($method)
	{
		if(! $this->methodExists($method)) {
			return null;
		}
		return $this->prepareMethodDetails($this->config['methods'][$method]);
	} 
}