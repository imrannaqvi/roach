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
	}
	
	function handle($request)
	{
		$this->request = $request;
		//
		$exception = null;
		$error = false;
		$response = null;
		//get request
		$post = $this->request->getPOST();
		$method = $post->get('method', '');
		$params = $post->get('params', array());
		//get details current request
		if(array_key_exists($method, $this->config['methods'])) {
			$item = $this->config['methods'][$method];
			if(! array_key_exists('authentication_required', $item)) {
				$authentication_required = true;
			} else {
				$authentication_required = $item['authentication_required'];
			}
			$user = $this->authentication->getStorage()->read($this->request->getHeader('Authorization'));
			if($authentication_required && !$user) {
				$error = 'authentication-required';
			}
			if(! $error) {
				//check if model is specified
				if(array_key_exists('model', $item)) {
					try {
						$model = $this->serviceLocator->get($item['model']);
						//check if model method is specified
						if(array_key_exists('method', $item)) {
							if(method_exists($model, $item['method'])) {
								try {
									$response = call_user_func_array(array($model, $item['method']), array($params));
								} catch( \Exception $e ) {
									$exception = array(
										'class' => get_class($e),
										'message' => $e->getMessage(),
										//'file' => $e->getFile().':'.$e->getLine(),
										//'stack_trace' => $e->getTrace()
									);
								}
							} else {
								$error = 'model-method-not-found';
							}
						} else {
							$error = 'model-method-not-defined';
						}
					} catch( \Zend\ServiceManager\Exception\ServiceNotFoundException $e) {
						$error = 'model-not-found-as-service';
					}
				} else {
					$error = 'model-not-defined';
				}
			}
		} else {
			$error = 'method-not-found';
		}
		return array(
			'error' => $error,
			'response' => $response,
			'method' => $method,
			'exception' => $exception
		);
	}
}