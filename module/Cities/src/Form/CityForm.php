<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 28.01.18
 * Time: 12:51
 */

namespace Cities\Form;


use Zend\Form\Form;

class CityForm extends Form
{
	public function __construct($name = null)
	{
		// We will ignore the name provided to the constructor
		parent::__construct('city');

		// by default: POST
		$this->setAttribute('method', 'POST');

		// fields
		$this->add([
			'name' => 'ID',
			'type' => 'hidden',
		]);
		$this->add([
			'name' => 'Name', // must be equals to this field name in db
			'type' => 'text',
			'options' => [
				'label' => 'Name',
			],
		]);

		// submit button
		$this->add([
			'name' => 'submit',
			'type' => 'submit',
			'attributes' => [
				'value' => 'Save',
				'id'    => 'submitbutton',
			],
		]);
	}
}