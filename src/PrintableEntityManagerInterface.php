<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableEntityManagerInterface.
 */

namespace Drupal\printable;

use Drupal\Core\Entity\EntityInterface;

/**
 * Entity manager interface for the printable module.
 */
interface PrintableEntityManagerInterface {

  /**
   * Gets the ID of the type of the entity.
   *
   * @param EntityInterface $entity
   *   The entity to check a printable version is available for.
   *
   * @return string
   *   The entity type ID.
   */
  public function getEntityName(EntityInterface $entity);

  /**
   * Get the entities that printable is available for.
   *
   * @return array
   *   An array of entity definitions keyed by the entity type.
   */
  public function getPrintableEntities();

  /**
   * Check if an entity has a printable version available for it.
   *
   * @param EntityInterface $entity
   *   The entity to check a printable version is available for.
   *
   * @return bool
   *   TRUE if the entity has a printable version available, FALSE if not.
   */
  public function isPrintableEntity(EntityInterface $entity);

  /**
   * Get the entities that Printable can generate hardcopies for.
   *
   * @return array
   *   An array of entity definitions keyed by the entity type.
   */
  public function getCompatibleEntities();

}
