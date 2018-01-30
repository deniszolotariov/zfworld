<?php

namespace Cities;

use Zend\Router\Http\Segment;

return [

	'router' => [
		'routes' => [
			'cities' => [
				'type'    => Segment::class,
				'options' => [
					'route' => '/cities[/:action[/:id]]',
					'constraints' => [
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					],
					'defaults' => [
						'controller' => Controller\CitiesController::class,
						'action'     => 'index',
					],
				],
			],
		],
	],

	'view_manager' => [
		'template_path_stack' => [
			'cities' => __DIR__ . '/../view',
		],
	],
];