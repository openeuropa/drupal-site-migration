<?php

namespace OpenEuropa\DrupalSiteMigration;

use Drupal\Driver\DrupalDriver;

/**
 * Load Drupal entities of a specific types.
 */
class EntityLoader
{
    /**
     * @var \Drupal\Driver\DrupalDriver
     */
    protected $driver;

    /**
     * SandboxCommand constructor.
     *
     * @param \Drupal\Driver\DrupalDriver $driver
     */
    public function __construct(DrupalDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Load Drupal entities.
     *
     * @param string $entityType
     *   Entity type to be loaded.
     * @param int|null $start
     *   The first entity from the result set to return. If NULL, removes any
     *   range directives that are set.
     * @param int|null $length
     *   The number of entities to return from the result set.
     *
     * @return array
     */
    public function loadEntities($entityType, $start = null, $length = null)
    {
        // Bootstrap Drupal.
        $this->driver->bootstrap();

        // Instantiate Drupal 7 EntityFieldQuery.
        $query = new \EntityFieldQuery();
        $query->entityCondition('entity_type', $entityType);

        // Add query range, useful for pagination in calling service.
        if ($start !== null && $length !== null) {
            $query->range($start, $length);
        }

        // Make sure we run queries as user 1 so we avoid permissions problems.
        $query->addMetaData('account', user_load(1));

        // Run query.
        $result = $query->execute();
        if (isset($result[$entityType])) {
            $ids = array_keys($result[$entityType]);
            return entity_load($entityType, $ids);
        }

        return [];
    }
}
