<?php

namespace OpenEuropa\DrupalSiteMigration\Command;

use \OpenEuropa\DrupalSiteMigration\Drupal\Driver;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use OpenEuropa\DrupalSiteMigration\Drupal\EntityLoader;
use OpenEuropa\DrupalSiteMigration\ProcessorManager;
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
     * @var \OpenEuropa\DrupalSiteMigration\ProcessorManager
     */
    protected $processorManager;

    /**
     * SandboxCommand constructor.
     *
     * @param \League\Fractal\Manager $manager
     * @param \OpenEuropa\DrupalSiteMigration\Drupal\Driver $driver
     * @param \OpenEuropa\DrupalSiteMigration\ExportWriter $exportWriter
     * @param \OpenEuropa\DrupalSiteMigration\ProcessorManager $processorManager
     */
    public function __construct(Manager $manager, Driver $driver, ExportWriter $exportWriter, ProcessorManager $processorManager)
    {
        $this->manager = $manager;
        $this->driver = $driver;
        $this->exportWriter = $exportWriter;
        $this->processorManager = $processorManager;

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
            // @todo: add support for multilingualism.
            $language = 'und';

            $resource = new Item($entity, function ($entity) use ($entityType, $bundle, $language) {
                $id = $this->driver->getEntityId($entityType, $bundle);
                $properties = [
                    'id' => $id,
                    'type' => $bundle,
                    'links' => [
                        'self' => $bundle . '/' . $id
                    ]
                ];

                $this->processorManager->process($properties, $entity, $entityType, $bundle, $language);

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
