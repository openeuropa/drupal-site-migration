<?php

namespace OpenEuropa\DrupalSiteMigration\Command;

use \OpenEuropa\DrupalSiteMigration\Drupal\Driver;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use OpenEuropa\DrupalSiteMigration\Drupal\EntityLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenEuropa\DrupalSiteMigration\ExportWriter;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $manager;

    /**
     * @var \OpenEuropa\DrupalSiteMigration\Drupal\Driver
     */
    protected $driver;

    /**
     * @var \OpenEuropa\DrupalSiteMigration\ExportWriter
     */
    protected $exportWriter;

    /**
     * SandboxCommand constructor.
     *
     * @param \League\Fractal\Manager $manager
     * @param \OpenEuropa\DrupalSiteMigration\Drupal\Driver $driver
     * @param \OpenEuropa\DrupalSiteMigration\ExportWriter $exportWriter
     */
    public function __construct(Manager $manager, Driver $driver, ExportWriter $exportWriter)
    {
        $this->manager = $manager;
        $this->driver = $driver;
        $this->exportWriter = $exportWriter;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('export')
            ->addArgument('type', InputArgument::REQUIRED, 'Entity type, e.g. "node".')
            ->addArgument('bundle', InputArgument::REQUIRED, 'Entity bundle, e.g. "article".')

            ->setDescription('Export entities of given bundle.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entityType = $input->getArgument('type');
        $bundle = $input->getArgument('bundle');

        // Bootstrap Drupal.
        $this->driver->bootstrap();

        // Clear previous export, if any.
        $this->exportWriter->clear($entityType, $bundle);

        // Loop on entities and export them.
        $entities = $this->driver->loadEntities($entityType, $bundle);

        // Start progress bar.
        $total = count($entities);
        $io->progressStart($total);

        foreach ($entities as $entity) {
            $resource = new Item($entity, function ($entity) use ($entityType, $bundle) {
                $properties = [
                    'links' => [
                        'self' => $bundle . '/' . $this->driver->getEntityId($entityType, $bundle)
                    ]
                ];

                $properties += [
                    'type' => $entity->type,
                    'id' => $entity->nid,
                    'title' => $entity->title,
                ];

                return $properties;
            }, $bundle);

            $content = $this->manager->createData($resource)->toJson(JSON_PRETTY_PRINT);
            $this->exportWriter->write($entityType, $bundle, 'und', $entity->nid, $content);

            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->success("{$total} entities exported to " . $this->exportWriter->getContentPath($entityType, $bundle));
    }
}
