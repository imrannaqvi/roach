<?php
namespace API\Service\RpcServer;

class RpcServer
{
	/** @var null|API\Service\RpcServer\Config Used for storing config object. */
	protected $config = null;
	
	/** @var null|Zend\ServiceManager\ServiceLocatorInterface Used to save reference to service locator for loading models as invokable. */
	protected $serviceLocator = null;
	
	/** @var null|API\Service\RpcServer\Response */
	protected $response;
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 * @param Zend\Authentication\AuthenticationService $authentication
	 * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 */
	function __construct(
		$config,
		\Zend\Authentication\AuthenticationService $authentication, 
		\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	) {
		//config
		$this->config = new Config($config);
		//authentication
		$this->authentication = $authentication;
		//service locator
		$this->serviceLocator = $serviceLocator;
		//response object
		$this->response = new Response();
	}
	
	/**
	 * To handle incoming request.
	 *
	 * @param Zend\Http\PhpEnvironment\Request
	 *
	 * @return array
	 */
	function handle(\Zend\Http\PhpEnvironment\Request $request)
	{
		$this->request = $request;
		//get request
		if(
			$this->request->getHeaders('Accept') &&
			$this->request->getHeaders('Accept')->getFieldValue() === 'application/json, text/plain, */*'
		) {
			$post = new \Zend\Stdlib\Parameters((array) json_decode(file_get_contents('php://input')));
		} else {
			$post = $this->request->getPOST();
		}
		$this->response->method = $method = $post->get('method', '');
		$params = $post->get('params', array());
		//get details current request
		if(! $this->config->methodExists($method)) {
			return $this->response->setError('method-not-found')->toArray();
		}
		//get method details from config and carry on
		$item = $this->config->getMethodDetails($method);
		$user = false;
		if(
			$this->authentication && (
				$this->config->authenticationRequired ||
				$item['authentication_required']
			)
		) {
			$user = $this->authentication->getStorage()->read($this->request->getHeader('Authorization'));
		}
		//check if authentication is required but not passed
		if(
			$item['authentication_required'] &&
			! $user
		) {
			return $this->response->setError('authentication-required')->toArray();
		}
		//check if model is specified
		if(! array_key_exists('model', $item)) {
			return $this->response->setError('model-not-defined')->toArray();
		}
		// check if model is invokable
		try {
			$model = $this->serviceLocator->get($item['model']);
		} catch( \Zend\ServiceManager\Exception\ServiceNotFoundException $e) {
			return $this->response->setError('model-not-found-as-service')->toArray();
		}
		//check if model method is specified
		if(! array_key_exists('method', $item)) {
			return $this->response->setError('model-method-not-defined')->toArray();
		}
		// check if model method exists
		if(! method_exists($model, $item['method'])) {
			return $this->response->setError('model-method-not-found')->toArray();
		}
		// form filters and validations
		if(array_key_exists('form', $item)) {
			if(! $this->serviceLocator->has($item['form'])) {
				return $this->response->setException('form-not-found-as-service')->toArray();
			}
			$form = $this->serviceLocator->get($item['form']);
			$form->setData((array) $params);
			if(! $form->isValid()) {
				$this->response->setError($form->getMessages());
			} else {
				$params = (array) $form->getData();
			}
		}
		//dispatch api methods
		if(! $this->response->error) {
			try {
				$this->response->response = call_user_func_array(array(
					$model,
					$item['method']
				), array(
					new \Zend\Stdlib\Parameters((array) $params)
				));
			} catch( \Exception $e ) {
				$this->response->exception = array(
					'class' => get_class($e),
					'message' => $e->getMessage(),
					'file' => $e->getFile().':'.$e->getLine(),
					'stack_trace' => $e->getTrace()
				);
			}
		}
		return $this->response->toArray();
	}
}