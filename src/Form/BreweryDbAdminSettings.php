<?php

/**
 * @file
 * Contains \Drupal\brewerydb\Form\BreweryDbAdminSettings.
 */

namespace Drupal\brewerydb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class BreweryDbAdminSettings extends ConfigFormBase {

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    // Init.
    $this->setConfigFactory($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'brewerydb_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['brewerydb.settings'];
  }

  public function buildForm(array $form = [], FormStateInterface $form_state) {
    $config = $this->configFactory->get('brewerydb.settings');

    $form['brewerydb_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('brewerydb_api_key'),
      '#description' => $this->t('API Key used to verify access to the BreweryDB.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('brewerydb.settings')
      ->set('brewerydb_api_key', $form_state->getValue('brewerydb_api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
