<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\Derivative\PrintableLinksBlock.
 */

namespace Drupal\printable\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\printable\PrintableEntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Local tasks plugin derivative to provide a tab for each printable format.
 */
class PrintableLinksBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The printable entity manager.
   *
   * @var \Drupal\printable\PrintableEntityManagerInterface.
   */
  protected $printableEntityManager;

  /**
   * Construct a new printable format links block.
   *
   * @param \Drupal\printable\PrintableEntityManagerInterface $printable_entity_manager
   *   The printable entity manager.
   */
  public function __construct(PrintableEntityManagerInterface $printable_entity_manager) {
    $this->printableEntityManager = $printable_entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('printable.entity_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->printableEntityManager->getPrintableEntities() as $entity_type => $entity_definition) {
      $this->derivatives[$entity_type] = $base_plugin_definition;
      $this->derivatives[$entity_type]['admin_label'] .= t(' (' . $entity_definition->getLabel() . ')');
    }
    return $this->derivatives;
  }

}
