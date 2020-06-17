<?php

namespace OVS\Controllers\Admin;

use OVS\Controllers\AbstractController;
use OVS\Controllers\UserController;
use OVS\Core\Router;
use OVS\Models\UserModel;

class UserManagerController extends AbstractController {

	public function add_user() {
		$errors = [];
		$post = isset($_POST) ? $_POST : "";
		$base = new UserController($this->di, $this->request);
		$errors = $base->add($post);
		$props = ["user" => $post, "errors" => $errors];
		return $this->render("admin/users/user.add.twig", $props);
	}

	public function edit_user( int $user_id ) {
		$base = new UserController($this->di, $this->request);
		$result = $base->edit($user_id);
		if( isset($result["errors"]["exception"]["NotFoundException"]) ) {
			Router::redirect("admin/users");
		}
		$props = [
			"user" => $result["data"],
			"user_id" => $user_id,
			"meta" => $result["user_meta"],
			"errors" => $result["errors"]
		];
		return $this->render("admin/users/user.edit.twig", $props);
	}

	public function delete_user( int $user_id ) {
		$base = new UserController($this->di, $this->request);
		$errors = $base->delete($user_id);
		$error_string = "";
		$isDeleted = "false";
		foreach( $errors as $error_key => $error_msg ) {
			if($error_key === "success") {
				$isDeleted = "true";
			}
			// $msg = "delete=$isDeleted&msg=$error_msg[0]";
			$error_string .= "delete=$error_key&deletemsg=". urlencode("$error_msg[0]");
		}
		Router::redirect("admin/users/all?$error_string");
	}

	public function get_users() {
		$user_model = new UserModel($this->db);
		$post_obj = isset($_POST) ? $_POST : "";
		$data = [];
		$errors = [];
		$role = "all";
		try {
			if( $post_obj && is_valid_user_role($post_obj["filter_role"]) ) {
				$role = $post_obj["filter_role"];
				$data = $user_model->get( "role", $role );
			} else {
				$data = $user_model->get();
			}
		} catch( \Exception $ex ) {
			$errors["notExists"][] = $ex->getMessage();
		}
		$props = [ "users" => $data, "errors" => $errors, "filter_role" => $role ];
		return $this->render("admin/users.twig", $props);
	}

}