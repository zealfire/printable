<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableLinkBuilderInterface.
 */

namespace Drupal\printable;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for building the printable links.
 */
interface PrintableLinkBuilderInterface {

  /**
   * Build a render array of the printable links for a given entity.
   *
   * @param EntityInterface $entity
   *   The entity to build the printable links for.
   *
   * @return array
   *   The render array of printable links for the passed in entity.
   */
  public function buildLinks(EntityInterface $entity = NULL);

}
