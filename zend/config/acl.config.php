<?php
return array(
	//permissions common to all resources, these permissions will be merged with permissions of each resource.
	'common_permissions' => array(
		'read', 'create', 'update', 'delete',
		'created.delete'
	),
	//resources as key and value as an array or permissions.
	'resources' => array(
		'role' => array(),
		'user' => array(),
		'organisation' => array(),
		'workflow' => array(),
		'project' => array(
			'workflow.update',
			'user.add', 'user.remove'
		),
		'task' => array(
			'assigned.update'
		),
		'comment' => array(),
		'attachment' => array()
	)
);