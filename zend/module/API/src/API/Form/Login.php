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
				),
				'validators' => array(
					array(
						'name' => 'Regex',
						'options' => array(
							'pattern' => '/^[a-z0-9_.-]{1,50}+$/', // contain only a to z 0 to 9 underscore, hypen and space, min 1 max 50
							'pattern_js' => '^[a-zA-Z0-9_\.\-]{1,50}$' 
						)
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
				),
				'validators' => array(
					array(
						'name' => 'Regex',
						'options' => array(
							'pattern' => '/^[a-z0-9_.-]{6,25}+$/', // contain only a to z 0 to 9 underscore, hypen and space, min 1 max 50
							'pattern_js' => '^[a-zA-Z0-9_\.\-]{6,25}$' 
						)
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