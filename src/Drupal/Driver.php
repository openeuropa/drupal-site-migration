<?php


namespace OpenEuropa\DrupalSiteMigration\Drupal;

use Drupal\Driver\DrupalDriver;

/**
 * Extend Drupal driver.
 */
class Driver extends DrupalDriver
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap()
    {
        // Make sure we bootstrap Drupal only once.
        if (!$this->isBootstrapped()) {
            parent::bootstrap();
        }
    }
}
