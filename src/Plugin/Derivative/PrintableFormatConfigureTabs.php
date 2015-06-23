<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\Derivative\PrintableFormatConfigureTabs.
 */

namespace Drupal\printable\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\printable\PrintableFormatPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Local tasks plugin derivative to provide a tab for each printable format.
 */
class PrintableFormatConfigureTabs extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The printable format plugin manager.
   *
   * @var \Drupal\printable\PrintableFormatPluginManager.
   */
  protected $printableFormatManager;

  /**
   * Construct a new printable format configuration tab plugin derivative.
   *
   * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
   *   The printable format plugin manager.
   */
  public function __construct(PrintableFormatPluginManager $printable_format_manager) {
    $this->printableFormatManager = $printable_format_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('printable.format_plugin_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->printableFormatManager->getDefinitions() as $key => $definition) {
      $this->derivatives[$key] = $base_plugin_definition;
      $this->derivatives[$key]['title'] = $definition['title'];
      $this->derivatives[$key]['route_parameters'] = array('printable_format' => $key);
      $this->derivatives[$key]['route_name'] = 'printable.format_configure_' . $key;
    }
    return $this->derivatives;
  }

}
