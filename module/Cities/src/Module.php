<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 27.01.18
 * Time: 19:03
 */

namespace Cities;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}

	public function getServiceConfig()
	{
		return [
			// todo: change to factory class
			'factories' => [

				Model\CityTable::class => function($container) {
					$tableGateway = $container->get(Model\CityTableGateway::class);
					return new Model\CityTable($tableGateway);
				},

				Model\CityTableGateway::class => function ($container) {
					$dbAdapter = $container->get(AdapterInterface::class);
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\City());
					return new TableGateway('city', $dbAdapter, null, $resultSetPrototype);
				},
			],
		];
	}

	public function getControllerConfig()
	{
		return [
			'factories' => [
				Controller\CitiesController::class => function($container) {
					return new Controller\CitiesController(
						$container->get(Model\CityTable::class)
					);
				},
			],
		];
	}}