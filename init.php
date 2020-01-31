<?php
function autoloader($className) {
	$lastSlash = strpos($className, '\\') + 1;
	$className = substr($className, $lastSlash);
	$directory = str_replace('\\', '/', $className);
	$fileName = __DIR__ . '/app/' . $directory . '.php';
	require_once($fileName);
}
spl_autoload_register('autoloader');