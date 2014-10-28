<?php
namespace API\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		$error = false;
		$response = false;
		//get config
		$config = $this->getServiceLocator()->get('Config');
		$authentication = $this->getServiceLocator()->get('API\Service\AuthenticationService');
		if(! array_key_exists('roach', (array) $config)) {
			throw new \Exception('config.roach not found.');
		}
		$config = (array) $config['roach'];
		if(! array_key_exists('api', (array) $config)) {
			throw new \Exception('config.roach.api not found.');
		}
		$config = (array) $config['api'];
		//get request
		$request = $this->getRequest()->getPOST();
		$method = $request->get('method', '');
		$params = $request->get('params', array());
		//get details current request
		if(array_key_exists($method, $config['methods'])) {
			$item = $config['methods'][$method];
			if(! array_key_exists('authentication_required', $item)) {
				$authentication_required = true;
			} else {
				$authentication_required = $item['authentication_required'];
			}
			$user = $authentication->getStorage()->read($this->getRequest()->getHeader('Authorization'));
			if($authentication_required && !$user) {
				$error = 'authentication-required';
			}
			if(! $error) {
				//check if model is specified
				if(array_key_exists('model', $item)) {
					try {
						$model = $this->getServiceLocator()->get($item['model']);
						//check if model method is specified
						if(array_key_exists('method', $item)) {
							if(method_exists($model, $item['method'])) {
								$response = call_user_func_array(array($model, $item['method']), array($params));
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
		return new JsonModel(array(
			'error' => $error,
			'response' => $response,
			'method' => $method
		));
	}
}