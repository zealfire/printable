<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\PrintableFormatInterface.
 */

namespace Drupal\printable\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Defines an interface for printable format plugins.
 */
interface PrintableFormatInterface extends ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the administrative label for this format plugin.
   *
   * @return string
   *   The label of plugin.
   */
  public function getLabel();

  /**
   * Returns the administrative description for this format plugin.
   *
   * @return string
   *   The description of plugin.
   */
  public function getDescription();

  /**
   * Set the content for the printable response.
   *
   * @param array $content
   *   A render array of the content to be output by the printable format.
   */
  public function setContent(array $content);

  /**
   * Returns the response object for this format plugin.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getResponse();

}
