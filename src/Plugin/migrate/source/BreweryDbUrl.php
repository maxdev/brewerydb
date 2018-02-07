<?php

namespace Drupal\brewerydb\Plugin\migrate\source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\Plugin\migrate\source\Url;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving data via URLs with custom parameters.
 *
 * @MigrateSource(
 *   id = "brewerydb_url"
 * )
 */

class BreweryDbUrl extends Url implements ContainerFactoryPluginInterface {

  /**
   * The brewerydb.settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The current migration.
   *
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;

  /**
   * Constructs a new BreweryDbUrl object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, ConfigFactoryInterface $config_factory) {
    if (!is_array($configuration['urls'])) {
      $configuration['urls'] = [$configuration['urls']];
    }

    $this->config = $config_factory->get('brewerydb.settings');

    if (isset($configuration['urls'])) {
      foreach ($configuration['urls'] as $key => $url) {
        $configuration['urls'][$key] = $this->setApiParams($url);
      }
    }

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('config.factory')
    );
  }

  /**
   * Set API params.
   *
   * @param $url string
   *
   * @todo: set parameters dynamically.
   *
   * @return string
   */
  private function setApiParams($url) {
    $api_key = $this->config->get('brewerydb_api_key');
    // At least one parameter is required unless the API account is flagged as Premium, so we pass availableId=1.
    return $this->addGetParams($url, ['key' => $api_key, 'availableId' => 1, 'sort' => 'DESC']);
  }

  /**
   * Add get params.
   *
   * @param $url string
   * @param $add_params array
   *
   * @return string
   */
  private function addGetParams($url, $add_params) {
    $url_parts = parse_url($url);
    parse_str($url_parts['query'], $params);

    $url_parts['query'] = http_build_query($add_params);

    return  $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
  }
}
