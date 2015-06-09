<?php

/**
 * @file
 * Contains \Drupal\hardcopy\Plugin\HardcopyFormatBase.
 */

namespace Drupal\hardcopy\Plugin;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\hardcopy\LinkExtractor\LinkExtractorInterface;
use Drupal\Core\Render\Element\Html;
use Drupal\hardcopy\HardcopyCssIncludeInterface;
use Drupal\Core\Form\FormStateInterface;
use Wa72\HtmlPageDom\HtmlPage;
use Wa72\HtmlPageDom\HtmlPageCrawler;
 
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a base class for Filter plugins.
 */
abstract class HardcopyFormatBase extends PluginBase implements HardcopyFormatInterface, ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Hardcopy CSS include manager.
   *
   * @var \Drupal\hardcopy\HardcopyCssIncludeInterface
   */
  protected $hardcopyCssInclude;

  /**
   * Hardcopy link extractor.
   *
   * @var \Drupal\hardcopy\LinkExtractor\LinkExtractorInterface
   */
  protected $linkExtractor;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *  The config factory service.
   * @param \Drupal\hardcopy\HardcopyCssIncludeInterface $hardcopy_css_include
   *  The hardcopy CSS include manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactory $config_factory, HardcopyCssIncludeInterface $hardcopy_css_include, LinkExtractorInterface $link_extractor) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $config_factory;
    $this->hardcopyCssInclude = $hardcopy_css_include;
    $this->linkExtractor = $link_extractor;
    $this->configuration += $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id,  $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('config.factory'),
      $container->get('hardcopy.css_include'),
      $container->get('hardcopy.link_extractor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['title'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
    $this->configFactory->get('hardcopy.format')->set($this->getPluginId(), $this->configuration)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function setContent(array $content) {
    echo "<br>one<br>";
    $this->content = $content;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse() {
    echo "<br>two<br>";
    return new Response($this->getOutput());
  }
  /**
   * Build a render array of the content, wrapped in the hardcopy theme.
   *
   * @return array
   *  A render array representing the themed output of the content.
   */
  protected function buildContent() {
    echo "<br>five<br>";
    $build = array(
      '#theme' => array('hardcopy__' . $this->getPluginId(), 'hardcopy'),
      '#header' => array(
        '#theme' => array('hardcopy_header__' . $this->getPluginId(), 'hardcopy_header'),
      ),
      '#content' => $this->content,
      '#footer' => array(
        '#theme' => array('hardcopy_footer__' . $this->getPluginId(), 'hardcopy_footer'),
      ),
      '#attached' => array(
        'library' => array(
          array('system', 'jquery'),
          array('system', 'drupal'),
          array('hardcopy/css','drupal-hardcopy'),
          array('hardcopy/js','script'),
        ),
      ),
    );

    if ($include_path = $this->hardcopyCssInclude->getCssIncludePath()) {
      $build['#attached']['css'][] = $include_path;
    }

    // Eeeew. @todo remove this so we can unit test this method.
    //system_page_build($build);
    $build['#attached']['library'][] = 'hardcopy/drupal.hardcopy';
    $build['#attached']['js'] = array(
  'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js' => array(
    'type' => 'external',
  ),
);
    return $build;
  }

  /**
   * Get the HTML output of the whole page, ready to pass to the response
   * object.
   *
   * @return string
   *  The HTML string representing the output of this hardcopy format.
   */
  protected function getOutput() {
    echo "<br>three<br>";
    $content = $this->buildContent();
    echo "<br>six<br>";
    $rendered_page = render($content);
    echo "<br>ten<br>";
   if ($this->configFactory->get('hardcopy.settings')->get('extract_links')) {
      $rendered_page = $this->linkExtractor->extract((string) $rendered_page);
    }
    else {
      $rendered_page = $this->linkExtractor->removeAttribute((string) $rendered_page,'href');
    }
    return $rendered_page;
  }
}
