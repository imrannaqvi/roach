<?php
namespace API\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use API\Service\RpcServer;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		if(! array_key_exists('roach', (array) $config)) {
			throw new \Exception('config.roach not found.');
		}
		$config = (array) $config['roach'];
		if(! array_key_exists('api', (array) $config)) {
			throw new \Exception('config.roach.api not found.');
		}
		$config  = (array) $config['api'];
		$server = new RpcServer(
			$config,
			$this->getServiceLocator()->get('API\Service\AuthenticationService'),
			$this->getServiceLocator()
		);
		return new JsonModel(
			$server->handle($this->getRequest())
		);
	}
}