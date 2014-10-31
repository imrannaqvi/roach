<?php
return array(
	//permissions common to all resources, these permissions will be merged with permissions of each resource.
	'common_permissions' => array(
		'read', 'create', 'update', 'delete',
		'created.read',
		'created.update',
		'created.delete'
	),
	//resources as key and value as an array or permissions.
	'resources' => array(
		'role' => array(),
		'user' => array(),
		'organisation' => array(),
		'workflow' => array(),
		'project' => array(
			'assigned.read',
			'assigned.update',
			'workflow.update',
			'user.add', 'user.remove'
		),
		'task' => array(
			'assign',
			'assigned.read',
			'assigned.update'
		),
		'comment' => array(),
		'attachment' => array()
	),
	//default roles 
	'roles' => array(
		'root' => array(
			'*' => 'allow'
		),
		'orgnisation_manager' => array(
			'*' => 'deny',
			'extends' => 'project_manager',
			'organisation' => array(
				'allow' => array(
					'read',
					'update'
				),
			),
			'role' => array(
				'allow' => true
			),
			'project' => array(
				'allow' => array(
					'read',
					'create'
				)
			),
			'workflow' => true,
			'task' => true,
		),
		'project_manager' => array(
			'*' => 'deny',
			'extends' => 'project_user',
			'project'  array(
				'allow' => array(
					'assigned.read',
					'assigned.update'
					'workflow.update',
					'user.add', 'user.remove'
				),
			),
			'task' => array(
				'allow' => array(
					'assign'
				)
			),
		),
		'project_user' => array(
			'*' => 'deny',
			'task' => array(
				'allow' => array(
					'read',
					'create',
					'created.read',
					'created.update',
					'created.delete',
					'assigned.read',
					'assigned.update'
				),
			),
			'comment' => array(
				'allow' => array(
					'read',
					'create',
					'created.read',
					'created.update',
					'created.delete'
				),
			),
			'attachment' => array(
				'allow' => array(
					'read',
					'create',
					'created.read',
					'created.update',
					'created.delete'
				),
			),
		),
	),
);