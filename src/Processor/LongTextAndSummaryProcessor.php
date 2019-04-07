<?php

namespace OpenEuropa\DrupalSiteMigration\Processor;

/**
 * Process long text fields with summary.
 */
class LongTextAndSummaryProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(array &$properties, $entity, $language, array $configuration)
    {
        $destination = $configuration['destination'];
        $value = $this->getValue($entity, $configuration['source']);
        if ($value) {
            $properties[$destination . '_value'] = $value[$language][0]['safe_value'];
            $properties[$destination . '_summary'] = $value[$language][0]['safe_summary'];
            $properties[$destination . '_format'] = $value[$language][0]['format'];
        }
    }
}
