<?php

/**
 * @file
 * Contains \Drupal\printable\Annotation\PrintableFormat.
 */

namespace Drupal\printable\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an printable format annotation object.
 *
 * @Annotation
 */
class PrintableFormat extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the module providing the type.
   *
   * @var string
   */
  public $module;

  /**
   * The human-readable name of the format.
   *
   * This is used as an administrative summary of what the format does.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * Additional administrative information about the format's behavior.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation (optional)
   */
  public $description = '';

}
