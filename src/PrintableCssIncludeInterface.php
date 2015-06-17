<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableCssIncludeInterface.
 */

namespace Drupal\printable;

/**
 * Helper interface for the printable module.
 */
interface PrintableCssIncludeInterface {

  /**
   * Get the configured CSS include path for printable pages.
   *
   * @return string
   *   The include path, relative to the root of the Drupal install.
   */
  public function getCssIncludePath();

}
