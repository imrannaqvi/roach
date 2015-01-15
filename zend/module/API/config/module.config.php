<?php
return array(
	//define api methods and models here
	'roach' => array(
		'api' => array(
			'methods' => array(
				'login' => array(
					'model' => 'API\Model\Index',
					'method' => 'login',
					'authentication_required' => false,
					'form' => 'API\Form\Login'
				),
				'signup' => array(
					'model' => 'API\Model\Index',
					'method' => 'signup'
				),
				'logout' => array(
					'model' => 'API\Model\Index',
					'method' => 'logout'
				),
				'session'  => array(
					'model' => 'API\Model\Index',
					'method' => 'session'
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
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
		'display_not_found_reason' => true,
		'display_exceptions' => true,
		'doctype' => 'HTML5',
		'not_found_template' => 'error/404',
		'exception_template' => 'error/index',
		'template_map' => array(
			'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
			'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
			'error/404' => __DIR__ . '/../view/error/404.phtml',
			'error/index' => __DIR__ . '/../view/error/index.phtml',
		),
	),
);
