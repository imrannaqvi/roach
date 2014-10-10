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
			'assigned.read',
			'assigned.update'
		),
		'comment' => array(),
		'attachment' => array()
	)
);