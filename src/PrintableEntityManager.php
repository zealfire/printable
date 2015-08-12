<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableEntityManager.
 */

namespace Drupal\printable;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Helper class for the printable module.
 */
class PrintableEntityManager implements PrintableEntityManagerInterface {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The entity definitions of entities that have printable versions available.
   *
   * @var array
   */
  protected $compatibleEntities = array();

  /**
   * Constructs a new PrintableEntityManager object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory service.
   */
  public function __construct(EntityManagerInterface $entity_manager, ConfigFactoryInterface $config_factory) {
    $this->entityManager = $entity_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityName(EntityInterface $entity) {
    return $entity->getEntityTypeId();
  }

  /**
   * {@inheritdoc}
   */
  public function getPrintableEntities() {
    $compatible_entities = $this->getCompatibleEntities();
    $entities = array();
    foreach ($this->configFactory->get('printable.settings')->get('printable_entities') as $entity_type) {
      if (isset($compatible_entities[$entity_type])) {
        $entities[$entity_type] = $compatible_entities[$entity_type];
      }
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function isPrintableEntity(EntityInterface $entity) {
    return array_key_exists($entity->getEntityTypeId(), $this->getPrintableEntities());
  }

  /**
   * {@inheritdoc}
   */
  public function getCompatibleEntities() {
    // If the entities are yet to be populated, get the entity definitions from
    // the entity manager.
    if (empty($this->compatibleEntities)) {
      foreach ($this->entityManager->getDefinitions() as $entity_type => $entity_definition) {
        // If this entity has a render controller, it has a printable version.
        if ($entity_definition->hasHandlerClass('view_builder')) {
          $this->compatibleEntities[$entity_type] = $entity_definition;
        }
      }
    }
    return $this->compatibleEntities;
  }

}
