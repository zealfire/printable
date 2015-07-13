<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\Block\PrintableLinksBlock.
 */

namespace Drupal\printable\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\printable\PrintableLinkBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
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
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routematch;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $routematch, PrintableLinkBuilderInterface $link_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routematch = $routematch;
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
      $container->get('current_route_match'),
      $container->get('printable.link_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $form += parent::buildConfigurationForm($form, $form_state);
    $period = array(0, 60, 180, 300, 600, 900, 1800, 2700, 3600, 10800, 21600,
      32400, 43200, 86400,
    );
    $period = array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($period, $period));
    $period[0] = '<' . $this->t('no caching') . '>';
    $period[\Drupal\Core\Cache\Cache::PERMANENT] = $this->t('Forever');
    $form['cache'] = array(
      '#type' => 'details',
      '#title' => $this->t('Cache settings'),
    );
    $form['cache']['max_age'] = array(
      '#type' => 'select',
      '#title' => $this->t('Maximum age'),
      '#description' => $this->t('The maximum time this block may be cached.'),
      '#default_value' => $period[0],
      '#options' => $period,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_type = $this->getDerivativeId();
    if ($this->routematch->getMasterRouteMatch()->getParameter($entity_type)) {
      return array(
        '#theme' => 'links__entity__printable',
        '#links' => $this->linkBuilder->buildLinks($this->routematch->getMasterRouteMatch()->getParameter($entity_type)),
      );
    }
  }

}
