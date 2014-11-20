<?php
namespace API\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use API\Service\RpcServer;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		$server = new RpcServer(
			$this->getServiceLocator()->get('Config'),
			$this->getServiceLocator()->get('API\Service\AuthenticationService'),
			$this->getServiceLocator()
		);
		return new JsonModel(
			$server->handle($this->getRequest())
		);
	}
}