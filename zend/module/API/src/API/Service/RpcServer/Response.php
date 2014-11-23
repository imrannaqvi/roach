<?php
namespace API\Service\RpcServer;

class Response
{
	public $exception = null;
	public $error = false;
	public $response = null;
	public $method;
	
	public function setError($error)
	{
		$this->error = $error;
		return $this;
	}
	
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