<?php
namespace API\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class Form extends \Zend\Form\Form implements InputFilterAwareInterface
{
	private $name;
	protected $attributes = array();
	protected $elements_data = array();
	private $inputFilter;
	
	public function __construct()
	{
		parent::__construct($this->name);
		$this->addelements_data();
	}
	
	private function addelements_data()
	{
		for($i=0; $i<count($this->elements_data); $i++) {
			$this->add($this->elements_data[$i]);
		}
	}
	
	public function getInputFilter()
	{
		if(! $this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			for($i=0; $i<count($this->elements_data); $i++) {
				if(array_key_exists('validation', $this->elements_data[$i])) {
					$this->elements_data[$i]['validation']['name'] = $this->elements_data[$i]['name'];
					$inputFilter->add($factory->createInput($this->elements_data[$i]['validation']));
				}
			}
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}