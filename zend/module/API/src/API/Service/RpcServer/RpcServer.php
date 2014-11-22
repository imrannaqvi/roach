<?php
namespace API\Service\RpcServer;

class RpcServer
{
	protected $config = null;
	protected $serviceLocator = null;
	
	function __construct($config, $authentication, $serviceLocator)
	{
		//config
		$this->config = $config;
		//authentication
		$this->authentication = $authentication;
		//service locator
		$this->serviceLocator = $serviceLocator;
		//response object
		$this->response = new Response();
	}
	
	function handle($request)
	{
		$this->request = $request;
		//get request
		$post = $this->request->getPOST();
		$this->response->method = $method = $post->get('method', '');
		$params = $post->get('params', array());
		//get details current request
		if(! array_key_exists($method, $this->config['methods'])) {
			$this->response->error = 'method-not-found';
			return $this->response->toArray();
		}
		//get method details from config and carry on
		$item = $this->config['methods'][$method];
		if(! array_key_exists('authentication_required', $item)) {
			$authentication_required = true;
		} else {
			$authentication_required = $item['authentication_required'];
		}
		$user = $this->authentication->getStorage()->read($this->request->getHeader('Authorization'));
		//check if authentication is required but not passed
		if($authentication_required && !$user) {
			$this->response->error = 'authentication-required';
			return $this->response->toArray();
		}
		//check if model is specified
		if(! array_key_exists('model', $item)) {
			$this->response->error = 'model-not-defined';
			return $this->response->toArray();
		}
		// check if model is invokable
		try {
			$model = $this->serviceLocator->get($item['model']);
		} catch( \Zend\ServiceManager\Exception\ServiceNotFoundException $e) {
			$this->response->error = 'model-not-found-as-service';
			return $this->response->toArray();
		}
		//check if model method is specified
		if(! array_key_exists('method', $item)) {
			$this->response->error = 'model-method-not-defined';
			return $this->response->toArray();
		}
		// check if model method exists
		if(! method_exists($model, $item['method'])) {
			$this->response->error = 'model-method-not-found';
			return $this->response->toArray();
		}
		try {
			$this->response->response = call_user_func_array(array($model, $item['method']), array($params));
		} catch( \Exception $e ) {
			$this->response->exception = array(
				'class' => get_class($e),
				'message' => $e->getMessage(),
				//'file' => $e->getFile().':'.$e->getLine(),
				//'stack_trace' => $e->getTrace()
			);
		}
		return $this->response->toArray();
	}
}