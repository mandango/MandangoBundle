<?php

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Mandango\\MandangoBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../'.$path;
        return true;
    }
});

$vendorDir = __DIR__.'/../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'  => $vendorDir.'/symfony/src',
    'Mandango' => $vendorDir.'/mandango/src'
));
$loader->register();