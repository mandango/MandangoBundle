<?php

namespace Mandango\MandangoBundle\Tests\Logger;

use Mandango\MandangoBundle\Logger\MandangoLogger;

class MandangoLoggerTest extends \PHPUnit_Framework_TestCase
{
    private $logger;

    protected function setUp()
    {
        $this->logger = new MandangoLogger();
    }

    public function testLogQuery()
    {
        $query = array('foo' => 'bar');

        $kernelLogger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $kernelLogger
            ->expects($this->once())
            ->method('info')
            ->with('MongoDB Query: '.json_encode($query));

        $logger = new MandangoLogger($kernelLogger);
        $logger->logQuery($query);
    }

    public function testGetQueries()
    {
        $this->assertSame(array(), $this->logger->getQueries());
        $this->logger->logQuery($query1 = array('foo' => 'bar'));
        $this->logger->logQuery($query2 = array('ups' => 'foo'));
        $this->assertSame(array($query1, $query2), $this->logger->getQueries());
    }

    public function testCountQueries()
    {
        $this->assertSame(0, $this->logger->getNbQueries());
        $this->logger->logQuery(array());
        $this->assertSame(1, $this->logger->getNbQueries());
        $this->logger->logQuery(array());
        $this->assertSame(2, $this->logger->getNbQueries());
    }
}