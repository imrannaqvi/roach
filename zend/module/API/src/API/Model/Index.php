<?php
namespace API\Model;

class Index extends Model
{
	public function login($params)
	{
		$token = false;
		$acl = false;
		$resultRow = false;
		$this->authentication->getAdapter()
		->setIdentity($params['username'])
		->setCredential($params['password']);
		$result = $this->authentication->authenticate();
		if ($result->isValid()) {
			$resultRow = $this->authentication->getAdapter()->getResultRowObject();
			$resultRow = $this->authentication->getStorage()->write($resultRow);
			if(isset($resultRow->token)) {
				//set acl user
				$this->acl->setUser($resultRow);
				$acl = $this->acl->serialize();
				$token = $resultRow->token;
				//unset specific keys for response
				$resultRow = (array) $resultRow;
				foreach($resultRow as $key => $value) {
					if(in_array($key, array('password', 'token'))) {
						unset($resultRow[$key]);
					}
				}
			}
		}
		return array(
			'$token' => $token,
			'$user' => $resultRow,
			'$acl' => $acl
		);
	}
	
	public function session($params)
	{
		$resultRow = $this->authentication->getStorage()->read();
		//set acl user
		$this->acl->setUser($resultRow);
		$resultRow = (array) $resultRow;
		foreach($resultRow as $key => $value) {
			if(in_array($key, array('password', 'token'))) {
				unset($resultRow[$key]);
			}
		}
		return array(
			'$user' => $resultRow,
			'$acl' => $this->acl->serialize()
		);
	}
	
	public function signup($params)
	{
		return array();
	}
	
	public function logout()
	{
		$this->authentication->getStorage()->clear();
		return true;
	}
}