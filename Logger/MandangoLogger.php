<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * MandangoLogger.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoLogger
{
    private $logger;
    private $queries;

    /**
     * Constructor.
     *
     * @param LoggerInterface|logger $logger A logger (optional).
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->queries = array();
    }

    /**
     * Log a query.
     *
     * @param array $query The query.
     */
    public function logQuery(array $query)
    {
        $this->queries[] = $query;

        if ($this->logger) {
            $this->logger->info('MongoDB Query: '.json_encode($query));
        }
    }

    /**
     * Returns the queries.
     *
     * @return array The queries.
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Returns the number of queries.
     *
     * @return integer The number of queries.
     */
    public function getNbQueries()
    {
        return count($this->queries);
    }
}
