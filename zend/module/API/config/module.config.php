<?php
return array(
	//define api methods and models here
	'roach' => array(
		'api' => array(
			'methods' => array(
				'login' => array(
					'model' => 'API\Model\Index',
					'method' => 'login',
					'authentication_required' => false
				),
				'logout' => array(
					'model' => 'API\Model\Index',
					'method' => 'logout'
				)
			),
			'authentication_required' => true
		)
	),
	//api dispatcher
	'controllers' => array(
		'invokables' => array(
			'API\Controller\Index' => 'API\Controller\IndexController'
		),
	),
	//api route
	'router' => array(
		'routes' => array(
			'api' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'controller' => 'API\Controller\Index',
						'action' => 'index',
					),
				),
			)
		),
	),
	'view_manager' => array(
		'strategies' => array(
			'ViewJsonStrategy',
		),
	),
);
