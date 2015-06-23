<?php

/**
 * @file
 * Contains \Drupal\printable\LinkExtractor\LinkExtractorInterface.
 */

namespace Drupal\printable\LinkExtractor;

/**
 * Defines an interface for extracting links from a string of HTMl.
 */
interface LinkExtractorInterface {

  /**
   * Highlight hrefs from links in the given HTML string.
   *
   * @param string $string
   *   The HTML string to extract links from.
   *
   * @return string
   *   The HTML string, with links highlighted.
   */
  public function extract($string);

  /**
   * Remove href from links in the given HTML string.
   *
   * @param string $content
   *   The HTML string to remove links from.
   * @param string $attr
   *   The attribute which has to be removed from the link.
   *
   * @return string
   *   The HTML string, with links removed.
   */
  public function removeAttribute($content, $attr);

  /**
   * List the links at the bottom of page.
   *
   * @param string $content
   *   The HTML string which has links present.
   *
   * @return string
   *   The HTML string, containing links.
   */
  public function listAttribute($content);

}
