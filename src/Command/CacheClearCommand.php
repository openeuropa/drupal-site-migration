<?php

namespace OpenEuropa\DrupalSiteMigration\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear application cache.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_dir(__DIR__ . '/../../var/cache')) {
            $fs = new Filesystem();
            $fs->remove(__DIR__ . '/../../var/cache');
        }
        $output->writeln('Cache cleared.');
    }
}
