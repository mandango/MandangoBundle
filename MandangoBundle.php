<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle;

use Mandango\Container as MandangoContainer;
use Mandango\MandangoBundle\DependencyInjection\Compiler\MandangoMondatorPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MandangoBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        MandangoContainer::setLoader('default', array($this, 'loadMandango'));
        MandangoContainer::setDefaultName('default');
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        MandangoContainer::clear();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MandangoMondatorPass());
    }

    /**
     * Loads the mandango.
     */
    public function loadMandango()
    {
        return $this->container->get('mandango');
    }
}
