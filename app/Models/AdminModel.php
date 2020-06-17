<?php

namespace OVS\Models;

use OVS\Domain\Admin\Option;
use OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;

class AdminModel extends AbstractModel {

	public function set_option( Option $option ) {
		$option_name = $option->get_option_name();
		$option_value = $option->get_option_value();
		$query = <<<'SQL'
		INSERT INTO options (option_name, option_value)
		SELECT * FROM (SELECT :name, :value) AS tmp
		WHERE NOT EXISTS (
			SELECT option_name FROM options WHERE option_name = :name) LIMIT 1;
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("name", $option_name);
		$stmt->bindValue("value", $option_value);
		if( !$stmt->execute() )
			throw new DBException("Error adding option to db");
		
		$this->update_option($option_name, $option_value);
	}

	public function get_option(string $option_name) {
		$query = "SELECT * FROM options WHERE option_name = :name";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("name", $option_name);
		$stmt->execute();
		$options = $stmt->fetchAll($this->db::FETCH_ASSOC);
		return $options[0];
	}

	public function update_option(string $option_name, string $option_value) {
		$query = <<<'SQL'
		UPDATE options SET option_value=:value WHERE option_name=:name
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $option_value);
		$stmt->bindValue("name", $option_name);
		if(!$stmt->execute()) {
			throw new DBException("Error updating option");
		}
		return true;
	}

	public function get_all_options() {
		$query = "SELECT * FROM options";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$options = $stmt->fetchAll($this->db::FETCH_ASSOC);
		return $options;
	}

	public function get_all_options_alt() {
		$res = [];
		$query = "SELECT * FROM options";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$options = $stmt->fetchAll($this->db::FETCH_ASSOC);
		foreach($options as $index => $option) {
			$res[$option["option_name"]] = $option["option_value"];
		}
		return $res;
	}

}