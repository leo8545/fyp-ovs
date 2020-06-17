<?php

namespace OVS\Models;

use OVS\Domain\User;
use OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;

class UserModel extends AbstractModel {

	protected $tableName = "users";
	protected $fillable = ["id", "username", "email", "password", "role"];

	public function is_unique_field( string $field, string $value ) {
		$query = "SELECT * FROM `users` WHERE `$field` = '$value'";

		$stmt = $this->db->prepare($query);
		if( !$stmt->execute() ) {
			throw new DBException($stmt->errorInfo()[2]);
		}
		return ($stmt->rowCount() ? 0 : 1);
	}

	public function add_user_meta( int $user_id, string $meta_key, string $meta_value ) {
		$query = <<<'SQL'
		INSERT INTO user_meta (user_id, meta_key, meta_value)
		SELECT * FROM (SELECT :id, :key, :value) AS tmp
		WHERE NOT EXISTS (
			SELECT meta_key FROM user_meta WHERE meta_key = :key AND user_id = :id) LIMIT 1;
SQL;

		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $user_id);
		$stmt->bindValue("key", $meta_key);
		$stmt->bindValue("value", $meta_value);

		if( !$stmt->execute() ) {
			throw new DBException("Error inserting to usermeta");
		}

		$this->update_user_meta($user_id, $meta_key, $meta_value);
	}

	public function update_user_meta(int $user_id, string $meta_key, string $meta_value) {
		$query = <<<'SQL'
		UPDATE user_meta SET meta_value=:value WHERE meta_key=:key AND user_id = :id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("value", $meta_value);
		$stmt->bindValue("key", $meta_key);
		$stmt->bindValue("id", $user_id);
		if(!$stmt->execute()) {
			throw new DBException("Error updating option");
		}
		return true;
	}

	public function get_user_meta(int $user_id, bool $as_key_value_pair = true) {
		$query = <<<'SQL'
		SELECT * FROM user_meta WHERE user_id = :id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $user_id);
		if( !$stmt->execute() ) {
			throw new NotFoundException("Error finding usermeta");
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

	public function update_user(User $user) {
		$query = <<<'SQL'
		UPDATE users
		SET username=:name, email=:email, role=:role
		WHERE id=:id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $user->get_id());
		$stmt->bindValue("name", $user->get_name());
		$stmt->bindValue("email", $user->get_email());
		$stmt->bindValue("role", $user->get_role());
		if( !$stmt->execute() )
			throw new DBException("Error updating user");
	}

	public function update_password(User $user) {
		$query = <<<'SQL'
		UPDATE users
		SET password=:password
		WHERE id=:id
SQL;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id", $user->get_id());
		$stmt->bindValue("password", $user->get_password());
		if(!$stmt->execute())
			throw new DBException("Error updating user's password");
	}

}