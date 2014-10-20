<?php
return array(
	//define api methods and models here
	'roach' => array(
		'api' => array(
			'methods' => array(
				'login' => array(
					'model' => 'API\Model\Index',
					'method' => 'login'
				),
				'logout' => array(
					'model' => 'API\Model\Index',
					'method' => 'logout'
				)
			)
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
	)
);
