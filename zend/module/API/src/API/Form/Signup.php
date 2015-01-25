<?php
namespace API\Form;

class Signup extends Form
{
	private $name = 'signup';
	
	protected $attributes = array(
		'autocomplete' => 'off'
	);
	
	public $elements_data = array(
		array(
			'name' => 'first_name',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'First Name'
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
			'name' => 'last_name',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'Last Name'
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
			'name' => 'email',
			'attributes' => array(
				'type' => 'email'
			),
			'options' => array(
				'label' => 'Email'
			), 
			'validation' => array(
				'required' => true,
				'validators'=> array(
					array(
						'name' => 'EmailAddress',
					),
				),
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
			'name' => 'password2',
			'attributes' => array(
				'type'  => 'password',
				'error_msg' => 'Password mush match.'
			),
			'options' => array(
				'label' => 'Retype Password'
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