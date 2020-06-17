<?php

namespace OVS\Controllers;

use Exception;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Domain\Admin\Option;
use OVS\Domain\User;
use OVS\Models\AdminModel;
use OVS\Models\UserModel;
use OVS\Utils\Session;
use OVS\Utils\Validate;

class AdminController extends AbstractController {
	public function add_vehicle() {
		return $this->render("admin/vehicle.add.twig");
	}

	/**
	 * Admin dashboard
	 *
	 * @return void
	 */
	public function dashboard() {
		return $this->render("admin/dashboard.twig");
	}

	/**
	 * Admin settings
	 *
	 * @param string $option
	 * @return void
	 */
	public function settings($setting_page = "") {
		$template = "admin/settings";
		$post_obj = isset($_POST) ? $_POST : "";
		$admin_model = new AdminModel($this->db);
		$options = [];
		if($post_obj) {
			$options = $post_obj["options"];
			foreach( $options as $option_name => $option_value ) {
				$opt = new Option( $option_name, $option_value );
				$admin_model->set_option($opt);
			}
		}

		switch( $setting_page ) {
			case "menu":
				$template .= ".menu";
			break;
		}

		if( empty($options) ) {
			$options = $admin_model->get_all_options_alt();
		}

		$props = ["post_obj" => $post_obj, "options" => $options];
		return $this->render("$template.twig", $props);
	}

	public function appearance() {
		return $this->render("admin/settings.menu.twig");
	}

}