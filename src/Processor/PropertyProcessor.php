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
    public function process(array &$properties, $entity, $language, array $configuration)
    {
        $destination = $configuration['destination'];
        $properties[$destination] = $this->getValue($entity, $configuration['source']);
    }
}
