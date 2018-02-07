<?php

namespace Drupal\brewerydb\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;

/**
 * @QueueWorker(
 *   id = "brewerydb_beer_importer",
 *   title = @Translation("BreweryDB Beer Cron Importer"),
 *   cron = {
 *     "time" = 30,
 *   },
 * )
 */
class BreweryDbBeerImporterQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {
  public function __construct(PluginManagerInterface $migration_manager) {
    $this->migrationManager = $migration_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($container->get('plugin.manager.migration'));
  }

  public function processItem($item) {
    // @todo: make load of migration id dynamically from admin settings.
    $migration = $this->migrationManager->createInstance('beer_node');
    $message = new MigrateMessage('Beer Imported');
    $executable = new MigrateExecutable($migration, $message);
    $executable->import();
  }

}
