<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 27.01.18
 * Time: 21:11
 */

namespace Cities\Model;


use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class City implements InputFilterAwareInterface
{
	public $id;
	public $name;
	public $countryCode;
	public $district;
	public $population;

	public $countryName;
	public $countryLanguages;

	private $inputFilter;

	public function exchangeArray(array $data)
	{
		$this->id   		= !empty($data['ID']) 			? $data['ID'] 			: null;
		$this->name 		= !empty($data['Name']) 		? $data['Name'] 		: null;
		$this->countryCode  = !empty($data['CountryCode']) 	? $data['CountryCode'] 	: 'USA';
		$this->district  	= !empty($data['District']) 	? $data['District'] 	: '';
		$this->population  	= !empty($data['Population']) 	? $data['Population'] 	: 0;

		$this->countryName  		= !empty($data['CountryName']) 			? $data['CountryName'] 			: '';

		$this->countryLanguages  	= !empty($data['CountryLanguages']) 	? $data['CountryLanguages'] 	: '';
	}

	public function getArrayCopy()
	{
		return [
			'ID'     		=> $this->id,
			'Name' 			=> $this->name,
			'CountryCode'  	=> $this->countryCode,
			'District'  	=> $this->district,
			'Population'  	=> $this->population,
		];
	}
	/*
	 * Filters
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new DomainException(sprintf(
			'%s does not allow injection of an alternate input filter',
			__CLASS__
		));
	}

	public function getInputFilter()
	{
		if ($this->inputFilter) {
			return $this->inputFilter;
		}

		$inputFilter = new InputFilter();

		$inputFilter->add([
			'name' => 'ID',
			'required' => true,
			'filters' => [
				['name' => ToInt::class],
			],
		]);

		$inputFilter->add([
			'name' => 'Name',
			'required' => true,
			'filters' => [
				['name' => StripTags::class],
				['name' => StringTrim::class],
			],
			'validators' => [
				[
					'name' => StringLength::class,
					'options' => [
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 35,
					],
				],
			],
		]);

		$this->inputFilter = $inputFilter;
		return $this->inputFilter;
	}

}