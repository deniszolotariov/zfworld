<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 27.01.18
 * Time: 19:27
 */

namespace Cities\Controller;


use Cities\Form\CityForm;
use Cities\Model\City;
use Cities\Model\CityTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CitiesController extends AbstractActionController
{
	private $table;

	public function __construct(CityTable $table)
	{
		$this->table = $table;
	}

	public function indexAction()
	{
		$sorting = $this->params()->fromQuery('sort');
		if ( !is_array($sorting) ) $sorting = [];

		$paginator = $this->table->fetchAll(true, $sorting);

		// per page
		$paginator->setItemCountPerPage(15);

		// current page passed in query string,
		$page = (int) $this->params()->fromQuery('page', 1);
		if($page < 1) $page =1;
		$paginator->setCurrentPageNumber($page);

		return new ViewModel(['paginator' => $paginator, 'query' => $this->params()->fromQuery()]);
	}

	public function addAction()
	{
		$form = new CityForm();
		$form->get('submit')->setValue('Add');

		$request = $this->getRequest();

		if (! $request->isPost()) {
			return ['form' => $form];
		}

		$postData = $request->getPost();

		// force to Add (and prevent from Modify existing rows) => ID === 0
		$postData['ID'] = 0;

		// Validating
		$city = new City();
		$form->setInputFilter($city->getInputFilter());
		$form->setData($postData);

		if (! $form->isValid()) {
			return ['form' => $form];
		}

		// Adding
		$city->exchangeArray($form->getData());
		try {
			$this->table->saveCity($city);
		} catch (\Exception $e) {
			return ['form' => $form, 'error' => $e->getMessage()];
		}
		return $this->redirect()->toRoute('cities');	}

	public function editAction()
	{
		$request = $this->getRequest();
		$isAjax = $request ->isXmlHttpRequest();

		if ( $isAjax ) {
			$view = new \Zend\View\Model\ViewModel();
			$view->setTerminal(true);
		}

		$id = (int) $this->params()->fromRoute('id', 0);

		if (!$id) {
			return $this->redirect()->toRoute('cities', ['action' => 'add']);
		}


		try {
			$city = $this->table->getCity($id);	// raises an exception if the city is not found
		} catch (\Exception $e) {
			return $this->redirect()->toRoute('cities', ['action' => 'index']);
		}

		$form = new CityForm();
		$form->bind($city);
		$form->get('submit')->setAttribute('value', 'Edit');

		$viewData = ['id' => $id, 'form' => $form];

		if (! $request->isPost()) {
			return $viewData;
		}

		$form->setInputFilter($city->getInputFilter());
		$post = $request->getPost()->toArray();
		$post['ID'] = $id; // user ID from request
		$form->setData($post);

		if (! $form->isValid()) {
			if ( $isAjax ) {
				$form_messages = $form->getMessages();
				$error_str = '';
				foreach ($form_messages as $index => $form_message) {
					$error_str .= 'Error in "'.$index.'": ';
					foreach ($form_message as $index => $item) {
						$error_str .= $item .'. ';
					}
					$error_str .= PHP_EOL;
				}
				echo json_encode
				([
					'status' => 'error',
					'error' => $error_str,
				]);
				exit;
			}
			return $viewData;
		}

		try {
			$this->table->saveCity($city);
		} catch (\Exception $e) {
			if ( $isAjax ) {
				echo json_encode
				([
					'status' => 'error',
					'error' => $e->getMessage(),
				]);
				exit;
			}
			return array_merge($viewData, ['error' => $e->getMessage()]);
		}

		// Redirect to city list
		if ( $isAjax ) {
			echo json_encode
			([
				'status' => 'success',
				'result' => $post,
			]);
			exit;
		}
		return $this->redirect()->toRoute('cities', ['action' => 'index']);
	}

	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('cities');
		}

		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');

			if ($del == 'Yes') {
				$id = (int) $request->getPost('id');
				$this->table->deleteCity($id);
			}

			// Redirect to list of citys
			return $this->redirect()->toRoute('cities');
		}

		return [
			'id'    => $id,
			'city'	=> $this->table->getCity($id),
		];
	}

}