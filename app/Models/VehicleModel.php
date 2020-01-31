<?php

namespace OVS\Models;

use OVS\Domain\Vehicle;
use OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;
use PDO;

class VehicleModel extends AbstractModel {

	const CLASSNAME = "\OVS\Domain\Vehicle";
	
	/**
	 * Gets vehicle by id
	 * @param int $vehicle_id Id of the vehicle
	 * @return Vehicle
	 */
	public function get(int $vehicle_id) : Vehicle {
	
		$query = "SELECT * FROM vehicles WHERE id = :id";
		$stmt = $this->db->prepare( $query );
		$stmt->execute(["id" => $vehicle_id]);
		$vehicles = $stmt->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
	
		if( empty($vehicles) ) {
			throw new NotFoundException("Vehicle with $vehicle_id not found");
		}
	
		return $vehicles[0];
	}

	public function get_all() {

		$query = "SELECT * FROM vehicles";
		$stmt = $this->db->prepare( $query );
		$stmt->execute();
		$vehicles = $stmt->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
		return $vehicles;

	}

	public function create( Vehicle $vehicle ) {
		$query = <<<'SQL'
		INSERT INTO vehicles(name, price, year) VALUES(:name, :price, :year)
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("name", $vehicle->get_name());
		$stmt->bindValue("price", $vehicle->get_price());
		$stmt->bindValue("year", $vehicle->get_year());
		if(!$stmt->execute()) {
			throw new DBException($stmt->errorInfo()[2]);
		}
		echo "m";
	}
}