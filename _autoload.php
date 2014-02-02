<?php
/**
 * Simple implementation for the PSR-0 autoloader
 */
use \Exception;

function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    $filePath = __DIR__ . '/../' . $fileName;
    if (is_file($filePath)) {
        require($filePath);
    }
}

spl_autoload_register("autoload");