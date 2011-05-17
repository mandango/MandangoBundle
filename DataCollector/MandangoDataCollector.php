<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
    private $logger;

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
