<?php

namespace OVS\Models;

use PDO;
use \OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;
use OVS\Utils\QueryBuilder;

abstract class AbstractModel {

	protected $db;
	protected $fillable = [];
	protected $tableName = "";
	protected $primaryKey = "id";

	public function __construct( PDO $db ) {
		$this->db = $db;
	}

	/**
	 * Gets data from the table
	 * 
	 * If no argument is supplied it retrieves all of the rows from the table. 
	 * If two arguments are supplied, it treats the first argument as column name and second as value to search.
	 *
	 * @return array
	 */
	public function get() {
		$query = "SELECT * FROM $this->tableName";
		if ( func_num_args() === 2 ) {
			$columnName = "";
			$isValid = false;
			$field_name = func_get_arg(0);
			$field_value = func_get_arg(1);
			foreach( $this->fillable as $field ) {
				switch( $field ) {
					case $field_name:
						$columnName = $field;
						$isValid = true;
					break;
				}
				if($isValid) break;
			}
			$query .= " WHERE $columnName = :val";
		}
		$stmt = $this->db->prepare($query);
		if( @$field_value )
			$stmt->bindValue("val", $field_value);
		if( !$stmt->execute() )
			throw new DBException("Error getting data from table:$this->tableName.");
		$result = $stmt->fetchAll($this->db::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Creates a new entry in the database
	 *
	 * @param OVS\Domain $object An object of the class in OVS\Domain namespace
	 * @return boolean True if created successfully, false otherwise
	 */
	public function create( $object ) : bool {
		$class = new \ReflectionClass( get_class($object) );
		if($class->getNamespaceName() !== "OVS\Domain") {
			return false;
		}
		$res = [];
		foreach( $class->getProperties() as $prop ) {
			$prop->setAccessible(true);
			// $res[$prop->getName()] = $prop->getValue($object);0
			$res[] = $prop->getValue($object);
		}
		$q = new QueryBuilder;
		$cols = $q->getCommaValues($this->fillable, ["id"], false);
		$values = $q->getCommaValues($res);
		$query = "INSERT INTO $this->tableName($cols) VALUES($values)";
		$stmt = $this->db->prepare($query);
		if( !$stmt->execute() )
			throw new DBException("Error inserting user to our database");
		return true;
	}
	
	/**
	 * Deletes a row form database
	 *
	 * @param integer $id id of the row
	 * @return boolean True on success, false otherwise
	 */
	public function delete(int $id) : bool {
		$entry = $this->get($this->primaryKey, $id);
		if( $entry ) {
			$query_deleteRow = "DELETE FROM $this->tableName WHERE $this->primaryKey=:id";
			$stmt = $this->db->prepare($query_deleteRow);
			$stmt->bindValue("id", $id);
			if(!$stmt->execute())
				throw new DBException("Error deleting entry");
			return true;
		}
		return false;
	}
}