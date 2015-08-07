<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\Derivative\PrintableLinksBlock.
 */

namespace Drupal\printable\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\printable\PrintableEntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Local tasks plugin derivative to provide a tab for each printable format.
 */
class PrintableLinksBlock extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

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
  public function __construct(PrintableEntityManagerInterface $printable_entity_manager, TranslationInterface $translation_manager) {
    $this->printableEntityManager = $printable_entity_manager;
    $this->stringTranslation = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('printable.entity_manager'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->printableEntityManager->getPrintableEntities() as $entity_type => $entity_definition) {
      $this->derivatives[$entity_type] = $base_plugin_definition;
      $this->derivatives[$entity_type]['admin_label'] = $this->t('@name (@entity_name)', array(
        '@name' => $this->derivatives[$entity_type]['admin_label'],
        '@entity_name' => $entity_definition->getLabel(),
        ));
    }
    return $this->derivatives;
  }

}
