<?php

/*
 * Copyright 2010 Pablo Díez <pablodip@gmail.com>
 *
 * This file is part of Mandango.
 *
 * Mandango is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mandango is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mandango. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mandango\MandangoBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * MandangoDataCollector.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoLogger
{
    protected $logger;
    protected $queries = array();

    /**
     * Constructor.
     *
     * @param LoggerInterface|logger $logger A logger (optional).
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
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
