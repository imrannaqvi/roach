<?php
namespace API\Service\RpcServer;

class Exception extends \Exception
{
	/** @var array Used for passing data. */
	private $_options;
	
	/**
	 * Constructor
	 *
	 * @param string $message
	 * @param int $code
	 * @param Exception|null $previous
	 * @param array $options
	 *
	 * @return boolean
	 */
	public function __construct(
		$message,
		$code = 0,
		Exception $previous = null,
		$options = array()
	) {
		parent::__construct($message, $code, $previous);
		$this->_options = $options;
	}
	
	/**
	 * Getter for options.
	 * @param array $options
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
}