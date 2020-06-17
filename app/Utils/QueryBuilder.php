<?php

namespace OVS\Utils;

class QueryBuilder {
	/**
	 * Converts an array into comma separated string
	 *
	 * @param array $fields Array to convert
	 * @param array $filter Fields to ignore
	 * @param bool $enquote Whether to enquote a string or not. (Default: true)
	 * @return string Comma separated string
	 */
	public function getCommaValues(array $fields, array $filter = [], bool $enquote = true) : string {
		$result = "";
		// Filters the $fields array by removing the field(s) given in the $filter array
		foreach( $filter as $f ) {
			if( array_search($f, $fields) !== false ) {
				unset($fields[array_search($f, $fields)]);
			}
		}
		foreach( $fields as $field ) {
			if(!empty($field)) {
				if($enquote) 
					$result .= "'";
				$result .= "$field";
				if($enquote) 
					$result .= "'";
				if( $field != end($fields) )
					$result .= ", ";
			}
		}
		return $result;
	}
	public function getUpdateSet($cols, $vals, $filter = "") {
		$query = "";
		$result = [];
		if( count($cols) === count($vals) ) {
			for( $i=0; $i < count($cols); $i++ ) {
				$result[$cols[$i]] = $vals[$i];
				if( $cols[$i] === $filter ) {
					unset($result[$cols[$i]]);
				}
			}
			foreach( $result as $c => $v ) {
				$query .= "$c=$v";
				if($v !== end($result)) {
					$query .= ", ";
				}
			}
		}
		return $query;
	}
}