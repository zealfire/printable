<?php

/**
 * @file
 * Contains \Drupal\hardcopy\HardcopyEntityManagerInterface
 */

namespace Drupal\hardcopy;

use Drupal\Core\Entity\EntityInterface;

/**
 * Entity manager interface for the hardcopy module.
 */
interface HardcopyEntityManagerInterface {

  /**
   * Get the entities that hardcopy is available for.
   *
   * @return array
   *  An array of entity definitions keyed by the entity type.
   */
  public function getHardcopyEntities();

  /**
   * Check if an entity has a hardcopy version available for it.
   *
   * @param EntityInterface $entity
   *  The entity to check a hardcopy version is available for.
   *
   * @return bool
   *  TRUE if the entity has a hardcopy version available, FALSE if not.
   */
  public function isHardcopyEntity(EntityInterface $entity);

  /**
   * Get the entities that Hardcopy can generate hardcopies for.
   *
   * @return array
   *  An array of entity definitions keyed by the entity type.
   */
  public function getCompatibleEntities();

}
