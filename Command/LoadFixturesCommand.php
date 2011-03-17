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

namespace Mandango\MandangoBundle\Command;

use Mandango\DataLoader;
use Mandango\MandangoBundle\Util;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * LoadFixturesCommand.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class LoadFixturesCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mandango:load-fixtures')
            ->setDescription('Load fixtures.')
            ->addOption('append', null, InputOption::VALUE_OPTIONAL, 'Whether or not to append the data fixtures', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('processing fixtures');

        $data = array();
        // application
        if (is_dir($dir = $this->container->getParameter('kernel.root_dir').'/fixtures/mandango')) {
            $finder = new Finder();
            foreach ($finder->files()->name('*.yml')->followLinks()->in($dir) as $file) {
                $data = Util::arrayDeepMerge($data, (array) Yaml::load($file));
            }
        }
        // bundles
        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
            if (is_dir($dir = $bundle->getPath().'/Resources/fixtures/mandango'))
            {
                $finder = new Finder();
                foreach ($finder->files()->name('*.yml')->followLinks()->in($dir) as $file) {
                    $data = Utile::arrayDeepMerge($data, (array) Yaml::load($file));
                }
            }
        }

        if (!$data) {
            $output->writeln('there are not fixtures');

            return;
        }

        $output->writeln('loading fixtures');

        $dataLoader = new DataLoader($this->container->get('mandango'));
        $dataLoader->load($data, true);
    }
}
