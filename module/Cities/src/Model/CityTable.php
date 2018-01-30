<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 27.01.18
 * Time: 21:17
 */

namespace Cities\Model;

use RuntimeException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CityTable
{
	private $tableGateway;

	public function __construct(TableGatewayInterface $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/* -------------------------------------------------
	* Get
	* -------------------------------------------------- */
	/*
	 * All
	 */
	public function fetchAll($paginated = false, $sorting = [])
	{
		if ($paginated) {
			return $this->fetchPaginatedResults($sorting);
		}
		return $this->tableGateway->select();
	}
	private function fetchPaginatedResults($sorting = [])
	{
		// Create a new Select object for the table:
		$select = new Select(['c' => $this->tableGateway->getTable()], ['Name' => 'c.Name']);
		$select->join(['cn' => 'country'], 'cn.Code = c.CountryCode', ['CountryName' => 'Name']);
		$select->join(['cl' => 'countrylanguage'], 'cl.CountryCode = c.CountryCode',
			['CountryLanguages' => new Expression('GROUP_CONCAT(cl.Language, "||", cl.IsOfficial,"||", cl.Percentage)')]);
		$select->group('c.ID');

		// Sorting
		if ($sorting) {
			$order = [];
			foreach ($sorting as $key => $val) {
				$val = strtoupper($val);
				if ( !in_array($val, ['ASC', 'DESC']) ) continue;
				$order[$key] = $val;

			}
			$select->order($order);
		}

		// Create a new result set based on the City entity:
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new City());

		// Create a new pagination adapter object:
		$paginatorAdapter = new DbSelect(
		// our configured select object:
			$select,
			// the adapter to run it against:
			$this->tableGateway->getAdapter(),
			// the result set to hydrate:
			$resultSetPrototype
		);

		$paginator = new Paginator($paginatorAdapter);
		return $paginator;
	}

	/*
	 * One
	 */
	public function getCity($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(['ID' => $id]);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find row with identifier %d',
				$id
			));
		}

		return $row;
	}

	/* -------------------------------------------------
	* Set - Update/Insert
	* -------------------------------------------------- */
	public function saveCity(City $city)
	{
		$data = [
			'Name' 			=> $city->name, // only can edit fields
			// 'CountryCode'  	=> $city->countryCode,
			// 'District'  	=> $city->district,
			// 'Population'  	=> $city->population,
		];

		$id = (int) $city->id;

		if ($id === 0) {
			$this->tableGateway->insert($data);
			return;
		}

		if (! $this->getCity($id)) {
			throw new RuntimeException(sprintf(
				'Cannot update city with identifier %d; does not exist',
				$id
			));
		}

		$this->tableGateway->update($data, ['ID' => $id]);
	}

	/* -------------------------------------------------
	* Delete
	* -------------------------------------------------- */
	public function deleteCity($id)
	{
		$this->tableGateway->delete(['ID' => (int) $id]);
	}
}