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
    'Symfony'           => $vendorDir.'/symfony/src',
    'Mandango\Mondator' => $vendorDir.'/mondator/src',
    'Mandango'          => $vendorDir.'/mandango/src',
    'Model'             => __DIR__
));
$loader->registerPrefixes(array(
    'Twig_' => $vendorDir.'/twig/lib',
));
$loader->register();

/*
 * Generate Mandango model.
 */
$configClasses = array(
    'Model\Article' => array(
        'fields' => array(
            'title' => array('type' => 'string'),
        ),
    ),
);

use Mandango\Mondator\Mondator;

$mondator = new Mondator();
$mondator->setConfigClasses($configClasses);
$mondator->setExtensions(array(
    new Mandango\Extension\Core(array(
        'metadata_factory_class'  => 'Model\Mapping\Metadata',
        'metadata_factory_output' => __DIR__.'/Model/Mapping',
        'default_output'          => __DIR__.'/Model'
    )),
));
$mondator->process();