<?php
namespace API\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		$out = array(
			'error' => false,
			'response' => false
		);
		//get config
		$config = $this->getServiceLocator()->get('Config');
		if(! array_key_exists('roach', (array) $config)) {
			throw new \Exception('config.roach not found.');
		}
		$config = (array) $config['roach'];
		if(! array_key_exists('api', (array) $config)) {
			throw new \Exception('config.roach.api not found.');
		}
		$config = (array) $config['api'];
		//get request
		$request = $this->getRequest()->getQuery();
		$method = $request->get('method', '');
		$data = $request->get('data', array());
		//get details current request
		if(array_key_exists($method, $config['methods'])) {
			$item = $config['methods'][$method];
			//check if model is specified
			if(array_key_exists('model', $item)) {
				try {
					$model = $this->getServiceLocator()->get($item['model']);
					//check if model method is specified
					if(array_key_exists('method', $item)) {
						if(method_exists($model, $item['method'])) {
							$out['response'] = call_user_func_array(array($model, $item['method']), array());
						} else {
							$out = array_merge($out, array(
								'error' => 'model-method-not-found'
							));
						}
					} else {
						$out = array_merge($out, array(
							'error' => 'model-method-not-defined'
						));
					}
				} catch( \Zend\ServiceManager\Exception\ServiceNotFoundException $e) {
					$out = array_merge($out, array(
						'error' => 'model-not-found-as-service'
					));
				}
			} else {
				$out = array_merge($out, array(
					'error' => 'model-not-defined'
				));
			}
		} else {
			$out = array_merge($out, array(
				'error' => 'method-not-found'
			));
		}
		header('Content-Type: application/json');
		die(json_encode(array_merge($out, array(
			'method' => $method,
			//'config' => $config
		))));
	}
}