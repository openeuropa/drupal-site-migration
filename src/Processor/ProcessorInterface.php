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
     * @param \stdClass $entity
     * @param string $field
     * @param array $configuration
     *
     * @return array
     *   List of field(s) to be added to the export object.
     */
    public function process(\stdClass $entity, $field, array $configuration);
}
