<?php

namespace OVS\Utils;

/**
 * Form validation class
 * 
 * @package OVS
 * @author Sharjeel Ahmad
 * @version 1.0.0
 * @access public
 */
class Validate {
	/**
	 * Posted field value
	 *
	 * @var string
	 * @access private
	 */
	private $field;

	/**
	 * Name of the field
	 *
	 * @var string
	 * @access private
	 */
	private $field_name;

	/**
	 * Rules to validate e.g. required|max:20|min:8
	 *
	 * @var string
	 * @access private
	 */
	private $rules;

	/**
	 * Errors array to store error messages
	 *
	 * @var array
	 * @access private
	 */
	private $errors = [];

	static private $model;

	/**
	 * Takes associative array of field name as key and rules as values and executes the corresponding validation method
	 *
	 * @param array $meta	name => rules
	 */
	public function __construct( array $meta, $model ) {
		self::$model = $model;
		foreach( $meta as $field => $rules ) {
			$this->field_name = $field;
			$this->field = (string) $_POST[$field];
			$this->rules = (string) $rules;
			$this->execute();
		}
	}

	/**
	 * Convert rules in string format to associative array
	 *
	 * @return array Associative array rule_name as key, rule_value as value
	 * @access protected
	 */
	protected function extract_rules() {
		$rules_arr = explode("|", $this->rules);
		$rules = [];

		foreach( $rules_arr as $rule_str ) {
			if( strpos( $rule_str, ":" ) !== false ) {
				$rule_arr = explode( ":", $rule_str );
				$rules[$rule_arr[0]] = $rule_arr[1];
			} else {
				$rules[$rule_str] = true;
			}
		}
		return $rules;
	}

	/**
	 * Runs validation method based on the rule
	 *
	 * @return void
	 * @access private
	 */
	private function execute() {
		$rules = $this->extract_rules();
		foreach( $rules as $rule => $value ) {
			switch( $rule ) {
				case "max":
					$this->set_errors( "max", $this->validate_max($value) );
				break;
				case "min":
					$this->set_errors( "min", $this->validate_min($value) );
				break;
				case "required":
					$this->set_errors( "required", $this->validate_required() );
				break;
				case "email":
					$this->set_errors("email", $this->validate_email());
				break;
				case "unique":
					$this->set_errors("unique", $this->validate_uniqueness());
				break;
			}
		}
	}

	/**
	 * Checks if user is unique
	 *
	 * @return integer|string	1 if unique, error message otherwise
	 * @access private
	 */
	private function validate_uniqueness() {
		// $user_model = new UserModel($this->db);

		$is_unique = 0;
		try {
			$is_unique = self::$model->is_unique_field($this->get_field_name(), $this->get_field());
		} catch( \Exception $ex ) {
			return $ex->getMessage();
		}
		return $is_unique !== 0 ? 1 : ucfirst($this->get_field_name()) . " already exists.";
	}

	/**
	 * Validation for maximum limit
	 *
	 * @param integer $value	Maximum limit
	 * @return integer|string	1 if true, error message if false
	 * @access private
	 */
	private function validate_max(int $value) {
		if( strlen($this->field) <= $value )
			return 1;
		return ucfirst($this->field_name) . " should be no longer than $value characters.";
	}

	/**
	 * Validation for minimum limit
	 *
	 * @param integer $value	Minimum limit
	 * @return integer|string	1 if true, error message if false
	 * @access private
	 */
	private function validate_min(int $value) {
		if( strlen($this->field) >= $value )
			return 1;
		return ucfirst($this->field_name) . " should be atleast $value characters long.";
	}

	/**
	 * Validation for required field
	 *
	 * @return integer|string	1 if true, error message if false
	 * @access private
	 */
	private function validate_required() {
		if( !empty($this->field) ) 
			return 1;
		return ucfirst($this->field_name) . " is a required field!";
	}

	/**
	 * Validate email
	 * 
	 * @return integer|string 1 if true, error message if false
	 * @access private
	 */
	private function validate_email() {
		return filter_var( $this->field, FILTER_VALIDATE_EMAIL ) ? 1 : "Invalid email!";
	}

	public function get_field_name() : string {
		return $this->field_name;
	}

	public function get_field() : string {
		return $this->field;
	}

	public function get_rules() : string {
		return $this->rules;
	}

	/**
	 * Gets errors
	 *
	 * @return array	e.g. [field] => [ [required] => 1 ]
	 * @access public
	 */
	public function get_errors() : array {
		return $this->errors;
	}

	public function set_errors($index, $msg) {
		$this->errors[$this->field_name][$index] = $msg;
	}

	/**
	 * Checks if the field is valid or not
	 *
	 * @return integer	1 if valid, 0 otherwise
	 * @access public
	 */
	public function is_valid() : int {
		foreach($this->errors as $f => $f_err) {
			foreach( $f_err as $error => $val ) {
				if($val !== 1)
					return 0;
			}
		}
		return 1;
	}
}