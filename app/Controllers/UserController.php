<?php

namespace OVS\Controllers;

use OVS\Core\Request;
use OVS\Utils\Validate;

class UserController extends AbstractController {
	public function register() {
		$request = new Request();
		$post_obj = isset($_POST) ? $_POST : "";
		$errors = [];
		if($post_obj) {
			$validate = new Validate( ["username" => "required|max:5", "email"	=> "required|min:5" ] );
			if( !$validate->is_valid() ) {
				$errors = $validate->get_errors();
			} else {
				$errors["success"][] = "You are registered successfully! Click here to <a href=\"/login\">login</a>.";
				// Add user to db

			}
		}
		$props = ["path" => $request->get_path(), "post_obj" => $post_obj, "errors" => $errors];
		return $this->render("register.user.twig", $props);
	}
}