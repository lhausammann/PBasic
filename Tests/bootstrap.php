<?php

$loaderPath = findAutoloader();
echo "Testing with autoloader from $loaderPath".PHP_EOL.PHP_EOL;

if (!$loader = @include $loaderPath) {
    echo <<<EOM
You must set up the project dependencies by running

    composer install

EOM;

    exit(1);
}

function findAutoloader()
{
    $ps = array(
        __DIR__.'/../../../../vendor/autoload.php',
        __DIR__.'/../vendor/autoload.php',
    );

    var_dump($ps);

    foreach ($ps as $path) {
        if (file_exists($path)) {
            return realpath($path);
        }
    }

    return false;
}