<?php

namespace Mandango\MandangoBundle\Tests;

use Mandango\Mandango;
use Mandango\Cache\ArrayCache;
use Mandango\Connection;
use Model\Mapping\Metadata;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $mandango;

    protected function setUp()
    {
        if (!class_exists('Mongo')) {
            $this->markTestSkipped('Mongo is not available.');
        }

        $this->mandango = new Mandango(new Metadata(), new ArrayCache());
        $this->mandango->setConnection('global', new Connection('mongodb://localhost:27017', 'mandango_bundle'));
        $this->mandango->setDefaultConnectionName('global');

        foreach ($this->mandango->getAllRepositories() as $repository) {
            $repository->getCollection()->drop();
        }
    }
}