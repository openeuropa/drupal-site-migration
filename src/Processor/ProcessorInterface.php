<?php


namespace OpenEuropa\DrupalSiteMigration\Processor;

/**
 * Define processor plugin interface.
 */
interface ProcessorInterface
{
    /**
     * Process export field.
     *
     * @param array $properties
     *    List or existing properties, the processor will had its fields here.
     * @param \stdClass $entity
     *    Source entity.
     * @param string $language
     *    Language code to be processed.
     * @param array $configuration
     *    Current processor configuration.
     */
    public function process(array &$properties, $entity, $language, array $configuration);
}
