<?php

set_time_limit(0);

if (!is_dir($vendorDir = __DIR__.'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

if (isset($argv[1])) {
    $_SERVER['SYMFONY_VERSION'] = $argv[1];
}

$deps = array(
    array('symfony', 'git://github.com/symfony/symfony', isset($_SERVER['SYMFONY_VERSION']) ? $_SERVER['SYMFONY_VERSION'] : '2.0'),
    array('twig', 'git://github.com/fabpot/Twig', 'origin/master'),
    array('mondator', 'git://github.com/mandango/mondator', 'origin/master'),
    array('mandango', 'git://github.com/mandango/mandango', 'origin/master'),
);

foreach ($deps as $dep) {
    if (3 === count($dep)) {
        list($name, $url, $rev) = $dep;
        $target = null;
    } else {
        list($name, $url, $rev, $target) = $dep;
    }

    if (null === $rev) {
        $rev = 'origin/master';
    }

    if (null !== $target) {
        $installDir = $vendorDir.'/'.$target;
    } else {
        $installDir = $vendorDir.'/'.$name;
    }

    $install = false;
    if (!is_dir($installDir)) {
        $install = true;
        echo "> Installing $name\n";

        system(sprintf('git clone -q %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    if (!$install) {
        echo "> Updating $name\n";
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}