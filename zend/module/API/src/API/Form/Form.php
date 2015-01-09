<?php
namespace API\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class Form extends Zend\Form\Form implements InputFilterAwareInterface
{
	private $name;
	protected $attributes = array();
	private $elements = array();
	private $inputFilter;
	
	public function __construct()
	{
		parent::__construct($this->name);
		$this->addElements();
	}
	
	private function addElements()
	{
		for($i=0; $i<count($this->elements); $i++) {
			$this->add($this->element[$i]);
		}
	}
	
	public function getInputFilter()
	{
		if(! $this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			for($i=0; $i<count($this->elements); $i++) {
				if(array_key_exists('validation', $this->elements[$i])) {
						$this->elements[$i]['validation']['name'] = $this->elements[$i]['name'];
						$inputFilter->add($factory->createInput($this->elements[$i]['validation']));
					}
				}
			}
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}