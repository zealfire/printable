<?php

/**
 * @file
 * Contains \Drupal\printable\Controller\PrintableController.
 */

namespace Drupal\printable\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\printable\PrintableFormatPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to display an entity in a particular printable format.
 */
class PrintableController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The printable format plugin manager.
   *
   * @var \Drupal\printable\PrintableFormatPluginManager
   */
  protected $printableFormatManager;

  /**
   * Constructs a \Drupal\printable\Controller\PrintableController object.
   *
   * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
   *   The printable format plugin manager.
   */
  public function __construct(PrintableFormatPluginManager $printable_format_manager) {
    $this->printableFormatManager = $printable_format_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.printableformat')
    );
  }

  /**
   * Returns the entity rendered via the given printable format.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be printed.
   * @param string $printable_format
   *   The identifier of the hadcopy format plugin.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The printable response.
   */
  public function showFormat(EntityInterface $entity, $printable_format) {
    if ($this->printableFormatManager->getDefinition($printable_format)) {
      $format = $this->printableFormatManager->createInstance($printable_format);
      $content = $this->entityManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'printable');
      $format->setContent($content);
      return $format->getResponse();
    }
    else {
      throw new NotFoundHttpException();
    }
  }

}
