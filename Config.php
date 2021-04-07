<?php

class Config {
	public static $baseDir = __DIR__;
}

new Config();

function autoloader($class)
{
    $file = Config::$baseDir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include $file;
}

spl_autoload_register('autoloader');