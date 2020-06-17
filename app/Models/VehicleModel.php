<?php

namespace OVS\Models;

use OVS\Domain\Vehicle;
use OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;
use OVS\Utils\Session;
use PDO;

class VehicleModel extends AbstractModel {

	const CLASSNAME = "\OVS\Domain\Vehicle";

	/**
	 * Retrieves vehicles and number of pages
	 *
	 * @param string $filter_query sql's 'WHERE' query
	 * @return array [vehicles, pages]
	 */
	public function get_all( string $filter_query = "", int $per_page = 10 ) {

		if( @$_GET && @$_GET["page"] ) {
			$page = (int) $_GET["page"];
		} else {
			$page = 1;
		}

		$vehicles_per_page = $per_page;
		$offset = ($page-1) * $vehicles_per_page;

		$total_vehicles_query = "SELECT COUNT(*) FROM vehicles ";
		if( !empty($filter_query) ) {
			$total_vehicles_query .= $filter_query . ";";
		}

		$stmt = $this->db->prepare($total_vehicles_query);
		if(!$stmt->execute()) {
			throw new DBException("Error getting vehicles!");
		}
		$total_vehicles = $stmt->fetchAll()[0][0];
		$total_pages = ceil( $total_vehicles / $vehicles_per_page );

		$new_query = "SELECT * FROM vehicles ";

		if(!empty($filter_query)) {
			$new_query .= $filter_query;
		}

		$new_query .= " LIMIT $offset, $vehicles_per_page;";
		$stmt_2 = $this->db->prepare( $new_query );

		if(!$stmt_2->execute()) {
			throw new DBException("Error getting vehicles!");
		}
		$vehicles = $stmt_2->fetchAll($this->db::FETCH_ASSOC);
		return [$vehicles, $total_pages];

	}

	public function create_vehicle( Vehicle $vehicle ) {
		$query = <<<'SQL'
		INSERT INTO vehicles(vehicle_number, vehicle_model, vehicle_manufacturer) VALUES(:vehicle_number, :vehicle_model, :vehicle_manufacturer)
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("vehicle_number", $vehicle->get_number());
		$stmt->bindValue("vehicle_model", $vehicle->get_model());
		$stmt->bindValue("vehicle_manufacturer", $vehicle->get_manufacturer());
		if(!$stmt->execute()) {
			throw new DBException($stmt->errorInfo()[2]);
		}
	}

	public function is_unique_field( string $field, string $value ) {
		$query = "SELECT * FROM `vehicles` WHERE `$field` = '$value'";

		$stmt = $this->db->prepare($query);
		if( !$stmt->execute() ) {
			throw new DBException($stmt->errorInfo()[2]);
		}
		return ($stmt->rowCount() ? 0 : 1);
	}

	public function get_vehicle_by(string $field, string $value, bool $single = true) {
		$col_name = "";
		switch( $field ) {
			case "id":
				$col_name = "vehicle_id";
				$value = (int) $value;
			break;
			case "number":
				$col_name = "vehicle_number";
			break;
			case "model":
				$col_name = "vehicle_model";
			break;
			case "manufacturer":
				$col_name = "vehicle_manufacturer";
			break;
		}
		$query = "SELECT * FROM vehicles WHERE $col_name = :value";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $value);
		if(!$stmt->execute()) {
			throw new DBException("Error getting vehicle.");
		}
		$user = $stmt->fetchAll($this->db::FETCH_ASSOC);
		if(!$user)
			throw new NotFoundException("No such vehicle exists.");
		return $single ? $user[0] : $user;
	}

	public function update_vehicle( Vehicle $vehicle ) {
		$query = <<<'SQL'
		UPDATE vehicles
		SET vehicle_number=:number, vehicle_model=:model, vehicle_manufacturer=:manufacturer
		WHERE vehicle_id=:id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $vehicle->get_id());
		$stmt->bindValue("number", $vehicle->get_number());
		$stmt->bindValue("model", $vehicle->get_model());
		$stmt->bindValue("manufacturer", $vehicle->get_manufacturer());
		if( !$stmt->execute() )
			throw new DBException("Error updating vehicle");
	}

	public function get_vehicle_models() {
		$query = "SELECT vehicle_model FROM vehicles";
		$stmt = $this->db->prepare($query);
		if(!$stmt->execute()) {
			throw new DBException("Error getting vehicle models!");
		}
		$data = $stmt->fetchAll($this->db::FETCH_ASSOC);
		$vehicle_models = [];
		foreach( $data as $model ) {
			if(!in_array($model["vehicle_model"], $vehicle_models))
				$vehicle_models[$model["vehicle_model"]] = $model["vehicle_model"];
		}
		return $vehicle_models;
	}

	public function get_vehicle_manufacturers() {
		$query = "SELECT vehicle_manufacturer FROM vehicles";
		$stmt = $this->db->prepare($query);
		if(!$stmt->execute()) {
			throw new DBException("Error getting vehicle manufacturers!");
		}
		$data = $stmt->fetchAll($this->db::FETCH_ASSOC);
		$vehicle_manufacturers = [];
		foreach( $data as $manufacturer ) {
			if(!in_array($manufacturer["vehicle_manufacturer"], $vehicle_manufacturers))
				$vehicle_manufacturers[$manufacturer["vehicle_manufacturer"]] = $manufacturer["vehicle_manufacturer"];
		}
		return $vehicle_manufacturers;
	}

	/**
	 * Filters vehicle based on the filter array provided
	 *
	 * @param array $filters [field_name => field_value]
	 * @return array 0: Vehicles, 1: Total pages
	 */
	public function filter_vehicle( array $filters ) {
		$query = " WHERE ";
		$i = 0;
		foreach( $filters as $key => $value ) {
			$query .= "`$key`=\"$value\"";
			if( sizeof($filters)-1 !== $i ) {
				$query .= " && ";
			}
			$i++;
		}
		
		$vehicles = $this->get_all($query)[0];
		$pages = $this->get_all($query)[1];

		return [$vehicles, $pages];
	}

	// Vehicle meta

	public function add_vehicle_meta( int $vehicle_id, string $meta_key, string $meta_value ) {
		$query = <<<'SQL'
		INSERT INTO vehicle_meta (vehicle_id, meta_key, meta_value)
		SELECT * FROM (SELECT :id, :key, :value) AS tmp
		WHERE NOT EXISTS (
			SELECT meta_key FROM vehicle_meta WHERE meta_key = :key AND vehicle_id = :id) LIMIT 1;
SQL;

		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $vehicle_id);
		$stmt->bindValue("key", $meta_key);
		$stmt->bindValue("value", $meta_value);

		if( !$stmt->execute() ) {
			throw new DBException("Error inserting to usermeta");
		}

		$this->update_vehicle_meta($vehicle_id, $meta_key, $meta_value);
	}

	public function update_vehicle_meta(int $vehicle_id, string $meta_key, string $meta_value) {
		$query = <<<'SQL'
		UPDATE vehicle_meta SET meta_value=:value WHERE meta_key=:key AND vehicle_id = :id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $meta_value);
		$stmt->bindValue("key", $meta_key);
		$stmt->bindValue("id", $vehicle_id);
		if(!$stmt->execute()) {
			throw new DBException("Error updating option");
		}
		return true;
	}

	public function get_vehicle_meta(int $vehicle_id, bool $as_key_value_pair = true) {
		$query = <<<'SQL'
		SELECT * FROM vehicle_meta WHERE vehicle_id = :id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $vehicle_id);
		if( !$stmt->execute() ) {
			throw new NotFoundException("Error finding vehiclemeta");
		}
		$meta = $stmt->fetchAll($this->db::FETCH_ASSOC);
		$res = [];
		if( $as_key_value_pair && $meta ) {
			foreach( $meta as $field ) {
				$res[ $field["meta_key"] ] = $field["meta_value"];
			}
			return $res;
		}
		return $meta;
	}

	public function delete_vehicle( int $vehicle_id ) {
		$query = <<<'SQL'
		DELETE FROM vehicles
		WHERE vehicle_id=:vehicle_id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("vehicle_id", $vehicle_id);
		if(!$stmt->execute())
			throw new DBException("Error deleting vehicle");
	}

	/**
	 * Retrives all the vehicles and their meta
	 * @param int $num how many vehicles to get
	 * @return array Associative array of vehicles with `vehicle id` as the key
	 */
	public function get_vehicles(int $num = 10) {
		$vehicles = $this->get_all("", $num)[0];
		$res = [];
		if( @$vehicles ) {
			foreach( $vehicles as $vehicle ) {
				$vehicle_id = $vehicle["vehicle_id"];
				$res[$vehicle_id] = $vehicle;
				$res[$vehicle_id]["meta"] = $this->get_vehicle_meta($vehicle_id);
			}
		}
		return $res;
	}

	public function book_vehicle(int $vehicle_id, $vehicle_price) {
		$vehicle = $this->get_vehicle_by("id", $vehicle_id);
		if( @$vehicle ) {
			$booker = Session::get("logged_in_username");
			$query = "INSERT INTO orders(booked_by, vehicle_id, order_price, booked_at) VALUES (:booker, :vehicle_id, :vehicle_price, CURRENT_TIMESTAMP())";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue("booker", $booker);
			$stmt->bindValue("vehicle_id", $vehicle_id);
			$stmt->bindValue("vehicle_price", $vehicle_price);
			if( !$stmt->execute() ) {
				throw new DBException("Error inserting order");
			}
			return true;
		}
		return false;
	}

	public function search_by_vehicle_models(string $keyword) {
		$query = "SELECT * FROM vehicles WHERE vehicle_model LIKE \"%$keyword%\"";
		$stmt = $this->db->prepare($query);
		if( !$stmt->execute() ) {
			return 'Error executing query';
		}
		return $stmt->fetchAll();
	}
}