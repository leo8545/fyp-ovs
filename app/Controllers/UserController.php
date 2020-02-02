<?php

namespace OVS\Controllers;

use Exception;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Domain\Admin;
use OVS\Domain\Customer;
use OVS\Domain\User;
use OVS\Models\UserModel;
use OVS\Utils\Session;
use OVS\Utils\Validate;

class UserController extends AbstractController {

	public function register() {
		$request = new Request();
		$path = $request->get_path();
		$post_obj = isset($_POST) ? $_POST : "";
		$errors = [];
		if($post_obj) {
			$user_model = new UserModel($this->db);
			$validate = new Validate( ["username" => "required|max:20|min:4|unique", "email" => "required|email|unique" ], $user_model );
			if( !$validate->is_valid() ) {
				$errors = $validate->get_errors();
			} else {
				// Add user to db
				if( $path === "/register" ) {
					$user = new Customer($post_obj["username"], $post_obj["email"], $post_obj["password"]);
					// $user = new Admin($post_obj["username"], $post_obj["email"], $post_obj["password"]);
				}
				try {
					$user_model->create_user($user);
				} catch( Exception $ex ) {
					$errors["db"][] = $ex->getMessage();
				}
				if( sizeof($errors) === 0 ) {
					$errors["success"][] = "You are registered successfully! Click here to <a href=\"/login\">login</a>.";
				}
			}
		}
		$props = ["path" => $path, "post_obj" => $post_obj, "errors" => $errors];
		return $this->render("register.user.twig", $props);
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
					$user = $user_model->get_user_by("username", $username);
					if( $user["username"] === $username && password_verify($password, $user["password"]) ) {
						Session::set("logged_in", "yes");
						Session::set("logged_in_as", $user["role"]);
						Session::set("logged_in_username", $user["username"]);
						if($user["role"] === "admin") {
							Router::redirect("admin/dashboard");
						} else if ($user["role"] === "customer") {
							Router::redirect("customer/dashboard");
						}
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
			Session::remove("logged_in");
			Session::remove("logged_in_as");
			Session::remove("logged_in_username");
			Router::redirect("");
		}
		return $this->render("home.twig");
	}

	public function go_to_dashboard() {
		if(Session::get("logged_in")) {
			$username = Session::get("logged_in_username");
			$role = Session::get("logged_in_as");
			$props = ["username" => $username];
			return $this->render("$role/dashboard.twig", $props);
		} else {
			Router::redirect("login");
		}
	}
}