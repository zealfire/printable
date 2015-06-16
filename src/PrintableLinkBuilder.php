<?php

/**
 * @file
 * Contains \Drupal\printable\PrintableLinkBuilder
 */

namespace Drupal\printable;

use Drupal\printable\PrintableFormatPluginManager;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Url;

/**
 * Helper class for the printable module.
 */
class PrintableLinkBuilder implements PrintableLinkBuilderInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The URL generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The printable format plugin manager.
   *
   * @var \Drupal\printable\PrintableFormatPluginManager
   */
  protected $printableFormatManager;

  /**
   * Constructs a new PrintableLinkBuilder object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *  The configuration factory service.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *  The URL generator service.
   * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
   *  The printable format plugin manager.
   */
  public function __construct(ConfigFactory $config_factory, UrlGeneratorInterface $url_generator, PrintableFormatPluginManager $printable_format_manager) {
    $this->configFactory = $config_factory;
    $this->urlGenerator = $url_generator;
    $this->printableFormatManager = $printable_format_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildLinks(EntityInterface $entity=NULL) {
    // Build the array of links to be added to the entity.
    $links = array();
    foreach ($this->printableFormatManager->getDefinitions() as $key => $definition) {
      $links[$key] = array(
      'title' => $definition['title'],
      'url' => Url::fromRoute('printable.show_format.' . $entity->getEntityTypeId(), array('printable_format' => $key, 'entity' => $entity->id())),
      );

      // Add target "blank" if the configuration option is set.
      if ($this->configFactory->get('printable.settings')->get('open_target_blank')) {
        $links[$key]['attributes']['target'] = '_blank';
      }
    }
    return $links;
  }
}
