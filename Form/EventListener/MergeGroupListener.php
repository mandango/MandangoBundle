<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Form\EventListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\FilterDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * MergeGroupListener.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MergeGroupListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::BIND_NORM_DATA => 'onBindNormData');
    }

    public function onBindNormData(FilterDataEvent $event)
    {
        $group = $event->getForm()->getData();
        $data = $event->getData();

        $group->replace($data);

        $event->setData($group);
    }
}
