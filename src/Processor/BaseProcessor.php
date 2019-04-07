<?php


namespace OpenEuropa\DrupalSiteMigration\Processor;

use \OpenEuropa\DrupalSiteMigration\Drupal\Driver;

abstract class BaseProcessor implements ProcessorInterface
{
    /**
     * @var \OpenEuropa\DrupalSiteMigration\Drupal\Driver
     */
    protected $driver;

    /**
     * BaseProcessor constructor.
     *
     * @param \OpenEuropa\DrupalSiteMigration\Drupal\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get field value from entity or its provided default if none set.
     *
     * @param \stdClass $entity
     * @param $field
     * @param null $default
     *
     * @return mixed|null
     */
    protected function getValue(\stdClass $entity, $field, $default = null)
    {
        return isset($entity->{$field}) ? $entity->{$field} : $default;
    }
}
