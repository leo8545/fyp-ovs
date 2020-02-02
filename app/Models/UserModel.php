<?php

namespace OVS\Models;

use OVS\Domain\User;
use OVS\Exceptions\DBException;

class UserModel extends AbstractModel {

	const CLASSNAME = "\OVS\Domain\User";

	public function get_user_by( string $field, string $value ) {
		$col_name = "";
		switch( $field ) {
			case "id":
				$col_name = "id";
				$value = (int) $value;
			break;
			case "name":
			case "username":
				$col_name = "username";
			break;
			case "email":
				$col_name = "email";
			break;
		}
		$query = "SELECT * FROM users WHERE $col_name = :value";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $value);
		if(!$stmt->execute()) {
			throw new DBException("Error executing database query while loggin in");
		}
		$user = $stmt->fetchAll($this->db::FETCH_ASSOC);
		return $user[0];
	}

	public function create_user( User $user ) {
		$name = $user->get_name();
		$email = $user->get_email();
		$password = $user->get_password();
		$role = $user->get_role();

		$query = <<<'SQL'
		INSERT INTO users(username, email, password, role) VALUES(:name, :email, :password, :role)
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("name", $name);
		$stmt->bindValue("email", $email);
		$stmt->bindValue("password", $password);
		$stmt->bindValue("role", $role);
		if( !$stmt->execute() )
			throw new DBException("Error inserting user to our database");
	}

	public function is_unique_field( string $field, string $value ) {
		$col_name = "";
		switch( $field ) {
			case "email":
				$col_name = "email";
			break;
			case "username":
				$col_name = "username";
			break;

		}
		$query = "SELECT * FROM users WHERE `$col_name`=:value";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $value);
		if(!$stmt->execute()) {
			throw new DBException(ucfirst($field) . " already exists!");
		}
		
		return sizeof($stmt->fetchAll($this->db::FETCH_ASSOC)) ? 0 : 1;
	}

	public function add_user_meta( int $user_id, string $meta_key, string $meta_value ) {
		$query = <<<'SQL'
		INSERT INTO user_meta(user_id, meta_key, meta_value) VALUES (:id, :key, :value)
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $user_id);
		$stmt->bindValue("key", $meta_key);
		$stmt->bindValue("value", $meta_value);

		if( !$stmt->execute() ) {
			throw new DBException("Error inserting to usermeta");
		}
	}

}