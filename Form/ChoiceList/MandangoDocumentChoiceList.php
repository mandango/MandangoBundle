<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Form\ChoiceList;

use Mandango\Query;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;

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
            $documents = call_user_func(array($this->class, 'getRepository'))->createQuery()->all();
        }
        $this->documents = $documents;

        $this->choices = array();
        foreach ($documents as $document) {
            if (null !== $this->field) {
                $value = $this->field;
            } elseif (method_exists($document, '__toString')) {
                $value = $document->__toString();
            } else {
                $value = $document->getId();
            }

            $this->choices[(string) $document->getId()] = $value;
        }
    }
}
