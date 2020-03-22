<?php
require_once __DIR__ . './vendor/autoload.php';
require_once __DIR__ . "/functions.php";

use OVS\Core\Config;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Models\AdminModel;
use OVS\Utils\DependencyInjector;
use OVS\Utils\Session;

$config = new Config();

/**
 * Database connection starts
 */

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

// Database connection ends

/**
 * Twig Loading starts
 */

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/views");
$twig = new \Twig\Environment($loader);

$admin_model = new AdminModel($db);
$options = $admin_model->get_all_options_alt();

$twig->addGlobal("app", $options);
$twig->addGlobal("session", Session::all());

$twig_functions = $config->get("Twig_Functions");

foreach( $twig_functions as $fn => $method ) {
	$twig->addFunction(new Twig\TwigFunction($fn, $method, ["is_safe" => ["html"]]));
}

// Twig Loading ends

/**
 * Dependency Injection starts
 */

$di = new DependencyInjector();
$di->set("PDO", $db);
$di->set('Twig_Environment', $twig);

// Dependency Injection ends

$route = new Router($di);
$request = new Request();

$route->route($request);
