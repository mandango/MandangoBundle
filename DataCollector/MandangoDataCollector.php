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

namespace Mandango\MandangoBundle\DataCollector;

use Mandango\MandangoBundle\Logger\MandangoLogger;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * MandangoDataCollector.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDataCollector extends DataCollector
{
    protected $logger;

    /**
     * Constructor.
     *
     * @param Mandango\MandangoBundle\Logger\MandangoLogger|null $logger A mandango logger (optional).
     */
    public function __construct(MandangoLogger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if ($this->logger) {
            $this->data['queries'] = $this->logger->getQueries();
        }
    }

    /**
     * Returns the queries.
     *
     * @return array The queries.
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * Returns the number of queries.
     *
     * @return integer The number of queries.
     */
    public function getNbQueries()
    {
        return count($this->data['queries']);
    }

    /**
     * Returns the time of the all queries (in milliseconds).
     *
     * @return integer The time of the all queries in milliseconds.
     */
    public function getTime()
    {
        $time = 0;
        foreach ($this->getQueries() as $query) {
            $time += $query['time'];
        }

        return $time;
    }

    /**
     * Returns the queries formatted.
     *
     * @return array The queries formatted.
     */
    public function getFormattedQueries()
    {
        $formattedQueries = array();
        foreach ($this->getQueries() as $query) {
            if (!isset($query['type'])) {
                print_r($query);
                exit();
            }
            $formattedQuery = array(
                'connection' => $query['connection'],
                'database'   => $query['database'],
                'type'       => $query['type'],
                'time'       => $query['time'],
            );

            foreach (array(
                'connection',
                'database',
                'type',
                'time',
            ) as $key) {
                unset($query[$key]);
            }

            $formattedQuery['query'] = Yaml::dump($query, 'batchInsert' == $formattedQuery['type'] ? 6 : 2);

            $formattedQueries[] = $formattedQuery;
        }

        return $formattedQueries;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mandango';
    }
}
