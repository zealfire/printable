<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\Block\PrintableLinksBlock.
 */

namespace Drupal\printable\Plugin\Block;

// @todo Remove use statements of unused classes.
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\printable\PrintableLinkBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a printable links block for each printable entity.
 *
 * @Block(
 *   id = "printable_links_block",
 *   admin_label = @Translation("Printable Links Block"),
 *   category = @Translation("Printable"),
 *   deriver = "Drupal\printable\Plugin\Derivative\PrintableLinksBlock"
 * )
 */
class PrintableLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The request service.
   *
   * @var \Symfony\Component\HttpFoundation\Request;
   */
  protected $request;

  /**
   * The printable link builder.
   *
   * @var \Drupal\printable\PrintableLinkBuilderInterface
   */
  protected $linkBuilder;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\printable\PrintableLinkBuilderInterface $link_builder
   *   The printable link builder.
   * @todo Update parameters
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Request $request, PrintableLinkBuilderInterface $link_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $request;
    $this->linkBuilder = $link_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('printable.link_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_type = $this->getDerivativeId();
    if (\Drupal::routeMatch()->getMasterRouteMatch()->getParameter($entity_type) && $entity_type == 'comment') {
      return array(
        '#theme' => 'links__entity__printable',
        '#links' => $this->linkBuilder->buildLinks(\Drupal::routeMatch()->getMasterRouteMatch()->getParameter('comment')),
      );
    }
    if ($this->request->attributes->has($entity_type)) {
      return array(
        '#theme' => 'links__entity__printable',
        '#links' => $this->linkBuilder->buildLinks($this->request->attributes->get($entity_type)),
      );
    }
  }

}
