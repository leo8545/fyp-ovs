<?php

namespace OVS\Controllers;

use Exception;
use OVS\Core\Router;
use OVS\Domain\Vehicle;
use OVS\Exceptions\DBException;
use OVS\Exceptions\NotFoundException;
use OVS\Models\UserModel;
use OVS\Models\VehicleModel;
use OVS\Utils\Session;
use OVS\Utils\Validate;

class VehicleController extends AbstractController {

	/**
	 * Get a vehicle by id
	 *
	 * @param integer $vehicle_id
	 * @return void
	 */
	public function get( int $vehicle_id ) {
		$vehicle = $this->get_vehicle_by("id", $vehicle_id)["data"][0];
		$errors = [];
		if( @$_POST ) {
			$errors = $this->book($vehicle_id, $_POST);
			// If vehicle is booked successfully
			if( @$errors["success"] ) {
				// Send user email
				$this->send_email($vehicle);
			}
		}
		$vehicle_model = new VehicleModel($this->db);
		$meta = $vehicle_model->get_vehicle_meta($vehicle_id);
		$user_model = new UserModel($this->db);
		$dealer = [];
		if(@$meta['vehicle_dealer']) {
			$_dealer = $user_model->get("id", explode("-", $meta['vehicle_dealer'])[1]);
			$dealer[$_dealer[0]['id']] = $_dealer[0]['username'];
		}
		$res = $vehicle;
		$res["meta"] = $meta;
		return $this->render( "vehicle.twig", [
			"vehicle" => $res, 
			"dealer" => $dealer,
			"errors" => $errors
		] );
	}

	public function get_vehicles() {
		$errors = [];
		$data = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->get_all()[0];
			$pages = $vehicle_model->get_all()[1];
		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		return ["data" => $data, "pages" => $pages, "errors" => $errors];
	}

	public function get_vehicle_by( string $field, string $value ) {
		$errors = [];
		$data = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->get_vehicle_by( $field, $value, false );
		} catch (Exception $ex) {
			$errors["exception"][] = $ex->getMessage();
		}
		return ["data" => $data, "errors" => $errors];
	}

	public function get_vehicle_models() {
		$errors = [];
		$data = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->get_vehicle_models();
		} catch( Exception $ex ) {
			$erros["exception"][] = $ex->getMessage();
		}
		return ["data" => $data, "errors" => $errors];
	}

	public function get_vehicle_manufacturers() {
		$errors = [];
		$data = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->get_vehicle_manufacturers();
		} catch( Exception $ex ) {
			$erros["exception"][] = $ex->getMessage();
		}
		return ["data" => $data, "errors" => $errors];
	}

	public function filter_vehicle( array $filters ) {
		$errors = [];
		$data = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->filter_vehicle( $filters )[0];
			$pages = $vehicle_model->filter_vehicle( $filters )[1];
		} catch (Exception $ex) {
			$errors["exception"][] = $ex->getMessage();
		}
		return ["data" => $data, "errors" => $errors, "pages" => $pages];
	}

	public function add( array $data ) {
		
		$errors = [];
		
		try {
			$vehicle_model = new VehicleModel($this->db);
			$number = isset($data["vehicle_number"]) ? $data["vehicle_number"] : "";
			$model = isset($data["vehicle_model"]) ? $data["vehicle_model"] : "";
			$manufacturer = isset($data["vehicle_manufacturer"]) ? $data["vehicle_manufacturer"] : "";
			$validate = new Validate(["vehicle_number" => "required|min:6|unique", "vehicle_model" => "required|min:2", "vehicle_manufacturer" => "required|min:2"], $vehicle_model);
			if( !$validate->is_valid() ) {
				$errors = $validate->get_errors();
			} else {
				$vehicle = new Vehicle( $number, $model, $manufacturer );
				$vehicle_model->create_vehicle($vehicle);
				$errors["success"][] = "New vehicle added successfully!";
			}

		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		return $errors;
	}

	public function edit( int $vehicle_id ) {
		$errors = [];
		$data = [];
		$vehicle_meta = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$data = $vehicle_model->get_vehicle_by("id", "$vehicle_id");
			$vehicle_meta = $vehicle_model->get_vehicle_meta($vehicle_id);
			$post = isset($_POST) ? $_POST : "";
			if($post) {
				$number = $post["vehicle_number"];
				$model = $post["vehicle_model"];
				$manufacturer = $post["vehicle_manufacturer"];
				$args = ["vehicle_number" => "required|min:6", "vehicle_model" => "required|min:2", "vehicle_manufacturer" => "required|min:2"];
				$validate = new Validate($args, $vehicle_model);

				if( !$validate->is_valid() ) {
					$errors = $validate->get_errors();
				} else {
					$vehicle = new Vehicle($number, $model, $manufacturer);
					$vehicle->set_id($vehicle_id);
					$vehicle_model->update_vehicle($vehicle);
					// Meta
					$meta = isset( $_POST["meta"] ) ? $_POST["meta"] : "";
					// Featured image
					if( @$_FILES["vehicle_image"]["name"] ) {
						$validate_image = new Validate(["vehicle_image" => "image|fileType:jpg,jpeg,png|maxSize:1M"], $vehicle_model);
						if(!$validate_image->is_valid()) {
							$errors = $validate_image->get_errors();
						} else {
							$file = $_FILES["vehicle_image"];
							$destination = dirname(__FILE__, 3) . "/uploads/images/" . basename( $file["name"] );
							if(!move_uploaded_file($file["tmp_name"], $destination)) {
								$errors["imageLoading"][] = "Error uploading image...";
							} else {
								if(@$meta) {
									$meta["vehicle_image"] = "/uploads/images/" . basename( $file["name"] );
								}
							}
						}
					}
					if( $meta ) {
						foreach( $meta as $meta_key => $meta_value ) {
							isset($meta[$meta_key]) && !empty($meta[$meta_key]) ? $vehicle_model->add_vehicle_meta($vehicle_id,$meta_key, $meta_value) : "";
						}
						$meta["vehicle_image"] = @$vehicle_meta["vehicle_image"];
						$vehicle_meta = $meta;
					}
				}
				$data = $post;
				if( count($errors) === 0 ) {
					$errors["success"][] = "Vehicle is updated successfully!";
				}
			}
		} catch( Exception $ex ) {
			$full_class_name = get_class($ex);
			$class = substr($full_class_name, strrpos($full_class_name, "\\")+1);
			$errors["exception"][$class] = $ex->getMessage();
		}
		return ["data" => $data, "vehicle_meta" => $vehicle_meta, "errors" => $errors];
	}

	public function delete( int $vehicle_id ) {
		$errors = [];
		try {
			$vehicle_model = new VehicleModel($this->db);
			$vehicle = $vehicle_model->get_vehicle_by( "id", $vehicle_id );
			if( $vehicle ) {
				$vehicle_model->delete_vehicle($vehicle_id);
			}
		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		$errors["success"][] = "Vehicle is deleted successfully!";
		return $errors;
	}

	/**
	 * Book a vehicle
	 * 
	 * @param int Vehicle id
	 * @param array Global post array of booking form
	 * @return array Messages with success or exception as first key
	 */
	public function book( int $vehicle_id, array $post ) {
		$is_booked = false;
		$errors = [];
		$booker = Session::get("logged_in_username");
		if( !@$booker && empty($booker) ) {
			Router::redirect("login");
			return;
		}
		try {
			$vehicle_model = new VehicleModel($this->db);
			$is_booked = $vehicle_model->book_vehicle($vehicle_id, $post["vehicle_price"]);
			if( $is_booked ) {
				$errors["success"][] = "Vehicle is booked successfully!";
			}
		} catch( Exception $ex ) {
			$errors["exception"][] = $ex->getMessage();
		}
		return $errors;
	}

	/**
	 * Sends an email
	 *
	 * @param array $data Data to be sent as message
	 * @return array Messages with success or exception as first key
	 */
	public function send_email( $data = null ) {
		$errors = [];
		// Get user
		$username = Session::get('logged_in_username');
		$user_model = new UserModel($this->db);
		try {
			$user = $user_model->get('username', $username);
			
			if( !@$user ) {
				throw new NotFoundException('User not found');
			}

			$user_email = $user[0]['email'];
			$subject = 'OVS :: New vehicle booked successfully!';
			$message = "
			Hi " . $user[0]['username'] . ",
			The following vehicle has been booked by you on OVS:
			Vehicle model: " . $data['vehicle_model'] . "
			Vehicle manufacturer: " . $data['vehicle_manufacturer'] . "
			Vehicle number: " . $data['vehicle_number'] . "
			";

			mail($user_email, $subject, $message );

		} catch( Exception $ex ) {
			$errors['exception'][] = $ex->getMessage();
		}
		return $errors;
	}

	/**
	 * Prints json response of search results of vehicles
	 *
	 * @return void
	 */
	public function search() {
		$vehicle_model = new VehicleModel($this->db);
		$keyword = $_POST['search'];
		$res = $vehicle_model->search_by_vehicle_models(urldecode($keyword));
		if( is_array($res) && count($res) === 0 ) {
			$res['notfound'] = true;
		}
		echo json_encode($res);
	}

	

}