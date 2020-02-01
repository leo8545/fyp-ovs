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

	/**
	 * Takes associative array of field name as key and rules as values and executes the corresponding validation method
	 *
	 * @param array $meta	name => rules
	 */
	public function __construct( array $meta ) {
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
	 * @access private
	 */
	private function extract_rules() {
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
					$this->errors[$this->field_name]["max"] = $this->validate_max($value);
				break;
				case "min":
					$this->errors[$this->field_name]["min"] = $this->validate_min($value);
				break;
				case "required":
					$this->errors[$this->field_name]["required"] = $this->validate_required();
				break;
			}
		}
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
	 * Gets errors
	 *
	 * @return array	e.g. [field] => [ [required] => 1 ]
	 * @access public
	 */
	public function get_errors() : array {
		return $this->errors;
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