<?php

namespace OpenEuropa\DrupalSiteMigration\Processor;

/**
 * Process simple entity properties, such as title, status, etc.
 */
class PropertyProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(\stdClass $entity, $field, array $configuration)
    {
        return [
            $field => $this->getValue($entity, $configuration['source']),
        ];
    }
}
