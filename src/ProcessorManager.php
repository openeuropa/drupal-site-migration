<?php


namespace OpenEuropa\DrupalSiteMigration;

use OpenEuropa\DrupalSiteMigration\Processor\ProcessorInterface;

class ProcessorManager
{
    /**
     * Export configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * @var \OpenEuropa\DrupalSiteMigration\Processor\ProcessorInterface[]
     */
    protected $processors;

    /**
     * ProcessorManager constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Add available processor managers.
     *
     * @param \OpenEuropa\DrupalSiteMigration\Processor\ProcessorInterface $processor
     *
     * @throws \ReflectionException
     */
    public function add(ProcessorInterface $processor)
    {
        $reflection = new \ReflectionClass($processor);
        $name = $reflection->getShortName();
        $this->processors[$name] = $processor;
    }

    /**
     * Process properties according to export configuration.
     *
     * @param array $properties
     * @param $entity
     * @param $entityType
     * @param $bundle
     * @param $language
     */
    public function process(array &$properties, $entity, $entityType, $bundle, $language)
    {
        if (!isset($this->configuration[$entityType][$bundle])) {
            throw new \RuntimeException("No export configuration found for {$entityType} of type {$bundle}.");
        }

        foreach ($this->configuration[$entityType][$bundle] as $key => $configuration) {
            if (!isset($this->processors[$configuration['processor']])) {
                throw new \RuntimeException("Processor {$configuration['processor']} not found on item {$key} for {$entityType} of type {$bundle}.");
            }
            /** @var ProcessorInterface $processor */
            $processor = $this->processors[$configuration['processor']];
            $processor->process($properties, $entity, $language, $configuration);
        }
    }
}
