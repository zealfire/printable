<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableFormatPluginManager.
 */

namespace Drupal\printable;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages printable format plugins.
 */
class PrintableFormatPluginManager extends DefaultPluginManager {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructs a PrintableFormatPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, ConfigFactory $config, ModuleHandlerInterface $module_handler) {
    $this->config = $config;
    parent::__construct('Plugin/PrintableFormat', $namespaces, $module_handler, 'Drupal\printable\Plugin\PrintableFormatInterface', 'Drupal\printable\Annotation\PrintableFormat');
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    $configuration += (array) $this->config->get('printable.format')->get($plugin_id);
    return parent::createInstance($plugin_id, $configuration);
  }

}
