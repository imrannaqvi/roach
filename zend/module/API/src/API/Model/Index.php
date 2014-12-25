<?php
namespace API\Model;

class Index extends Model
{
	public function login($params)
	{
		$token = false;
		$this->authentication->getAdapter()
		->setIdentity($params['username'])
		->setCredential($params['password']);
		$result = $this->authentication->authenticate();
		$resultRow = false;
		if ($result->isValid()) {
			$resultRow = $this->authentication->getAdapter()->getResultRowObject();
			$resultRow = $this->authentication->getStorage()->write($resultRow);
			if(isset($resultRow->token)) {
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
			'$user' => $resultRow
		);
	}
	
	public function logout()
	{
		$this->authentication->getStorage()->clear();
		return true;
	}
}