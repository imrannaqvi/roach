<?php
namespace API\Service\RpcServer;

class Response
{
	/** @var null|array */
	public $exception = null;
	
	/** @var mixed */
	public $error = false;
	
	/** @var null|array */
	public $response = null;
	
	/** @var string */
	public $method;
	
	/**
	 * Set Error for current response object.
	 *
	 * @param mixed.
	 *
	 * @return API\Service\RpcServer\Response
	 */
	public function setError($error)
	{
		$this->error = $error;
		return $this;
	}
	
	/**
	 * Return response as array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'error' => $this->error,
			'response' => $this->response,
			'method' => $this->method,
			'exception' => $this->exception
		);
	}
}