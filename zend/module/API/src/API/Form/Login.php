<?php
namespace API\Form;

class Login extends Form
{
	private $name = 'login';
	
	protected $attributes = array(
		'autocomplete' => 'off'
	);
	
	public $elements_data = array(
		array(
			'name' => 'username',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'Username'
			), 
			'validation' => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				)
			)
		),
		array(
			'name' => 'password',
			'attributes' => array(
				'type'  => 'password',
				'error_msg' => 'Enter Valid Password'
			),
			'options' => array(
				'label' => 'Password'
			), 
			'validation' => array(
				'required' => true,
				'filters'=> array(
					array(
						'name' => 'StripTags'
					), array(
						'name' => 'StringTrim'
					)
				)
			)
		),
		array(
			'name' => 'submit',
			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Login'
			)
		)
	);
}