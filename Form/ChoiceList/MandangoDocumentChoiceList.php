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

namespace Mandango\MandangoBundle\Form\ChoiceList;

use Mandango\Query;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;

/**
 * MandangoDocumentChoiceList.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentChoiceList extends ArrayChoiceList
{
    protected $class;
    protected $field;
    protected $query;
    protected $choices;

    protected $documents;

    public function __construct($class, $field = null, Query $query = null, array $choices = array())
    {
        $this->class = $class;
        $this->field = $field;
        $this->query = $query;
        $this->choices = $choices;

        parent::__construct($choices);
    }

    public function getDocuments()
    {
        if (null === $this->documents) {
            $this->load();
        }

        return $this->documents;
    }

    protected function load()
    {
        parent::load();

        if ($this->choices) {
            $documents = $this->choices;
        } elseif ($this->query) {
            $documents = $this->query->all();
        } else {
            $documents = call_user_func(array($this->class, 'query'))->all();
        }
        $this->documents = $documents;

        $this->choices = array();
        foreach ($documents as $document) {
            $value = null === $this->field ? $document->getId() : $document->get($this->field);
            $this->choices[$document->getId()->__toString()] = $value;
        }
    }
}
