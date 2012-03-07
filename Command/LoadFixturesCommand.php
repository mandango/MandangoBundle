<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Command;

use Mandango\DataLoader;
use Mandango\MandangoBundle\Util;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
class LoadFixturesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mandango:load-fixtures')
            ->setDescription('Load fixtures.')
            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL, 'The directory or file to load data fixtures from')
            ->addOption('append', null, InputOption::VALUE_NONE, 'Whether or not to append the data fixtures')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('processing fixtures');

        $dirOrFile = $input->getOption('fixtures');
        if ($dirOrFile) {
            $dirOrFile = array($dirOrFile);
        } else {
            $dirOrFile = array();
            // application
            if (is_dir($dir = $this->getContainer()->getParameter('kernel.root_dir').'/fixtures/mandango')) {
                $dirOrFile[] = $dir;
            }
            // bundles
            foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
                if (is_dir($dir = $bundle->getPath().'/Resources/fixtures/mandango')) {
                    $dirOrFile[] = $dir;
                }
            }
        }

        $files = array();
        foreach ($dirOrFile as $dir) {
            if (is_file($dir)) {
                $files[] = $dir;
                continue;
            }
            if (is_dir($dir)) {
                $finder = new Finder();
                foreach ($finder->files()->name('*.yml')->followLinks()->in($dir) as $file) {
                    $files[] = $file;
                }
                continue;
            }

            throw new \InvalidArgumentException(sprintf('"%s" is not a dir or file.', $dir));
        }

        $data = array();
        foreach ($files as $file) {
            $data = Util::arrayDeepMerge($data, (array) Yaml::parse($file));
        }

        if (!$data) {
            $output->writeln('there are not fixtures');

            return;
        }

        $output->writeln('loading fixtures');

        $dataLoader = new DataLoader($this->getContainer()->get('mandango'));
        $dataLoader->load($data, !$input->getOption('append'));
    }
}
