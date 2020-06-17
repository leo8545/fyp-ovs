<?php
namespace OVS\Controllers;

use Exception;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Domain\User;
use OVS\Exceptions\NotFoundException;
use OVS\Models\UserModel;
use OVS\Utils\Session;
use OVS\Utils\Validate;

class UserController extends AbstractController {
	public function add( $data ) {
		$errors = [];
		if( isset( $data ) && !empty( $data ) ) {
			$username = $data["username"];
			$email = $data["email"];
			$password = $data["password"];
			$role = $data["role"];
			try {
				$user_model = new UserModel($this->db);
				$validate = new Validate(["username" => "required|max:20|min:4|unique", "email" => "required|email|unique", "password" => "required|min:8" ], $user_model);
				if( !$validate->is_valid() ) {
					$errors = $validate->get_errors();
				} else {
					$user = new User($username, $email, $role);
					$user->set_password($password);
					// $user_model->create_user($user);
					$user_model->create($user);
					if( sizeof($errors) === 0 )
						$errors["success"][] = "New user added successfully!";
				}
			} catch( Exception $ex ) {
				$errors["notAdded"][] = $ex->getMessage();
			}
		}
		return $errors;
	}

	public function edit( int $user_id ) {
		
		$errors = [];
		$data = [];
		$user_meta = [];
		try {
			$user_model = new UserModel($this->db);
			// $data = $user_model->get_user_by("id", "$user_id");
			$data = $user_model->get( "id", $user_id )[0];

			$user_meta = $user_model->get_user_meta($user_id);
			$post = isset($_POST) ? $_POST : "";
			if($post) {
				$name = $post["username"];
				$email = $post["email"];
				$role = $post["role"];
				$args = ["username" => "required|max:20|min:4", "email" => "required|email"];
				if(isset($post["password"]) && !empty($post["password"])) {
					$password = $post["password"];
					$args["password"] = "required|min:8";
				}
				$validate = new Validate($args, $user_model);
				if( !$validate->is_valid() ) {
					$errors = $validate->get_errors();
				} else {
					$user = new User($name, $email, $role);
					$user->set_id($user_id);
					$user_model->update_user($user);
					if(isset($password) && !empty($password)) {
						$user->set_password($password);
						$user_model->update_password($user);
					}
					// Meta
					$meta = isset( $_POST["meta"] ) ? $_POST["meta"] : "";
					if( $meta ) {
						foreach( $meta as $meta_key => $meta_value ) {
							isset($meta[$meta_key]) && !empty($meta[$meta_key]) ? $user_model->add_user_meta($user_id,$meta_key, $meta_value) : "";
						}
						$user_meta = $meta;
					}
				}
				$data = $post;
				if( count($errors) === 0 ) {
					$errors["success"][] = "User is updated successfully!";
				}
			}
		} catch( Exception $ex ) {
			$full_class_name = get_class($ex);
			$class = substr($full_class_name, strrpos($full_class_name, "\\")+1);
			$errors["exception"][$class] = $ex->getMessage();
		}

		return ["data" => $data, "user_meta" => $user_meta, "errors" => $errors];
	}

	public function delete( int $user_id ) {
		$errors = [];
		try {
			$user_model = new UserModel($this->db);
			if( $user_model->delete($user_id) ) {
				$errors["success"][] = "User is deleted successfully";
			} else {
				$errors["exists"][] = "User with that id does not exists";
			}
		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		return $errors;
	}

	public function get( int $user_id ) {
		$errors = [];
		$user = [];
		$meta = [];
		try {
			$user_model = new UserModel($this->db);
			// $user = $user_model->get_user_by( "id", $user_id );
			$user = $user_model->get( "id", $user_id )[0];
			$meta = $user_model->get_user_meta( $user_id );
		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		return ["user" => $user, "meta" => $meta, "errors" => $errors];
	}

	public function login() {
		$request = new Request();
		$path = $request->get_path();
		$post_obj = isset($_POST) ? $_POST : "";
		$errors = [];
		if( !is_user_logged_in() ) {
			if($post_obj) {
				$username = (string) $post_obj["username"];
				$password = (string) $post_obj["password"];
				$user_model = new UserModel($this->db);
				try {
					// $user = $user_model->get_user_by("username", $username);
					
					if( ! @$user_model->get("username", $username) ) {
						throw new NotFoundException('User not found');
					}

					$user = $user_model->get("username", $username)[0];
					
					if( $user["username"] === $username && password_verify($password, $user["password"]) ) {
						Session::set([
							"logged_in" => "yes",
							"logged_in_as" => $user["role"],
							"logged_in_username" => $user["username"]
						]);
						if( is_valid_user_role( $user["role"] ) ) {
							Router::redirect($user["role"] . "/dashboard");
						}
					} else {
						$errors["notFound"][] = "Wrong username or password !";
					}
				} catch( Exception $ex ) {
					$errors["notFound"][] = "Wrong username or password !";
				}
			}
		} else {
			$errors["already"][] = "You are already logged in as <b>" . Session::get("logged_in_username") . "</b>. Click here to <a href=\"/logout\">logout</a>";
		}

		$props = ["path" => $path, "post_obj" => $post_obj, "errors" => $errors];
		return $this->render("login.user.twig", $props);
	}

	public function logout() {
		if(Session::get("logged_in")) {
			Session::remove(["logged_in", "logged_in_as", "logged_in_username"]);
			Router::redirect("");
		}
		return $this->render("home.twig");
	}
	
}