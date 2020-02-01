<?php
require_once __DIR__ . './vendor/autoload.php';

use OVS\Core\Config;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Utils\DependencyInjector;

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

$req = new Request();
$route = new Router($di);

$route->route($req);
