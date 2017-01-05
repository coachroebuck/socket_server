<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class validatingFieids {
	
	//************************************************************************************
	function __construct() {
	}

	//************************************************************************************
	function __destruct() {
	}

	public function getRegistrationFields() {
		return array(
				"firstName" => array(
					"isString" => true, 
					"comparison" => whereOperator::NotNull,
					"maximumLength" => MAXIMUM_SHORTFIELD_CHARACTERS,
					"isRequired" => true,
					"preAction" => null,
					"postAction" => null,
				),
				"lastName" => array(
					"isString" => true, 
					"comparison" => whereOperator::NotNull,
					"maximumLength" => MAXIMUM_SHORTFIELD_CHARACTERS,
					"isRequired" => true,
					"preAction" => null,
					"postAction" => null,
				),
				"nickName" => array(
					"isString" => true, 
					"comparison" => whereOperator::NotNull,
					"maximumLength" => MAXIMUM_SHORTFIELD_CHARACTERS,
					"isRequired" => true,
					"preAction" => null,
					"postAction" => null,
				),
				"email" => array(
					"isString" => true, 
					"comparison" => whereOperator::NotNull,
					"errorMessage" => "Email is missing",
					"maximumLength" => MAXIMUM_EMAIL_CHARACTERS,
					"isRequired" => true,
					"preAction" => null,
					"postAction" => "validateEmail",
				),
				"password" => array(
					"isString" => true, 
					"comparison" => whereOperator::NotNull,
					"errorMessage" => "Password is missing",
					"maximumLength" => MAXIMUM_SHORTFIELD_CHARACTERS,
					"isRequired" => true,
					"preAction" => null,
					"postAction" => null,
				),
		);
	}
}

?>