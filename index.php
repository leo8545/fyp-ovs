<?php
// require "init.php";
require_once __DIR__ . './vendor/autoload.php';

use OVS\Controllers\VehicleController;
use OVS\Core\Config;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Exceptions\DBException;
use OVS\Utils\DependencyInjector;
use OVS\Utils\Form;

$config = new Config();
$config_db = $config->get("db");
try {
	$db = new PDO(
		"mysql:host=" . $config_db["host"] . ";dbname=" . $config_db["dbname"],
		$config_db["user"],
		$config_db["password"]
	);
} catch( PDOException $ex ) {
	echo "ERROR:". $ex->getMessage();
}

/**
 * Twig Loading starts
 */

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/views");
$twig = new \Twig\Environment($loader);
$twig->addGlobal("n", "sharjeel");
$twig_functions = array(
	"Form_open" 		=> 		"\OVS\Utils\Form::open",
	"Form_close" 		=> 		"\OVS\Utils\Form::close",
	"Form_text" 		=> 		"\OVS\Utils\Form::text",
);
foreach( $twig_functions as $fn => $src ) {
	$twig->addFunction(new Twig\TwigFunction($fn, $src, ["is_safe" => ["html"]]));
}

// Twig Loading ends

$di = new DependencyInjector();
$di->set("PDO", $db);
$di->set('Twig_Environment', $twig);

$req = new Request();
$route = new Router($di);

$route->route($req);

// echo Form::group( array(
// 	"label" => array(
// 		"for" => "name",
// 		"text" => "Enter your name"
// 	),
// 	"field" => array(
// 		"type" => "text",
// 		"name" => "fname",
// 		"value" => "sharjeel",
// 		"attributes" => array(
// 			"class" => "hello world"
// 		)
// 	)
// ) );
// echo Form::radio("gender", "male");
// echo Form::label("male", "MALE");
// echo Form::radio("gender", "female");
// echo Form::label("female", "feMALE");

// $vc = new VehicleController( $di, $req );
// $vc->create("suzuki", "200", "2019");

// echo Form::open("/dashboard/vehicle/add");
// echo "hi";
// echo Form::submit("Click");
// echo Form::close();
