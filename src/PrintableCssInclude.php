<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableCssInclude.
 */

namespace Drupal\printable;

use Drupal\printable\PrintableCssIncludeInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * Helper class for the printable module.
 */
class PrintableCssInclude implements PrintableCssIncludeInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The theme handler service.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Constructs a new PrintableCssInclude object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory service.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandlerInterface $theme_handler) {
    $this->configFactory = $config_factory;
    $this->themeHandler = $theme_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getCssIncludePath() {
    if ($include_path = $this->configFactory->get('printable.settings')->get('css_include')) {
      if ($token = $this->extractCssIncludeToken($include_path)) {
        list(, $theme) = explode(':', trim($token, '[]'));
        $include_path = str_replace($token, $this->getThemePath($theme), $include_path);
      }
      return $include_path;
    }
  }

  /**
   * Extract the theme token from a CSS include path.
   *
   * @param string $path
   *   An include path (optionally) with a taken to extract in the form:
   *   "[theme:theme_machine_name]".
   *
   * @return string|NULL
   *   The extracted token in the form "[theme:theme_machine_name]" or NULL if
   *   no token exists in the string.
   */
  protected function extractCssIncludeToken($path) {
    $start = '[theme:';
    $end = ']';

    // Fail fast.
    if (strpos($path, $start) === FALSE) {
      return NULL;
    }

    $index = strpos($path, $start);
    // Here strpos is zero indexed.
    $length = strpos($path, $end, $index) + 1;

    return substr($path, $index, $length);
  }

  /**
   * Get the path to a theme.
   *
   * @param string $theme
   *   The machine name of the theme to get the path for.
   *
   * @return string
   *   The path to the given theme.
   *
   * @todo replace this with an injectable version of drupal_get_path() when/if
   *  it lands.
   */
  protected function getThemePath($theme) {
    $info = $this->themeHandler->listInfo();
    $path = '';
    if (isset($info[$theme])) {
      $path = dirname($info[$theme]->uri);
    }
    return $path;
  }

}
