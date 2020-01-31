<?php

namespace OVS\Models;

class UserModel extends AbstractModel {

	const CLASSNAME = "\OVS\Domain\User";

	public function get_user( int $user_id ) {

		$query = "SELECT * FROM users WHERE id = :id";
		$stmt = $this->db->prepare($query);

	}

}