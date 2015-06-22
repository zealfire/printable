<?php

/**
 * @file
 * Contains \Drupal\printable\Routing\RouteSubscriber.
 */

namespace Drupal\printable\Routing;

use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\printable\PrintableEntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Defines a route subscriber to generate print route for all content entities.
 */
class RouteSubscriber implements EventSubscriberInterface {

  /**
   * The printable entity manager service.
   *
   * @var \Drupal\printable\PrintableEntityManagerInterface
   */
  protected $printableEntityManager;

  /**
   * Constructs a printable RouteSubscriber object.
   *
   * @param \Drupal\printable\PrintableEntityManagerInterface $printable_entity_manager
   *   The printable entity manager service.
   */
  public function __construct(PrintableEntityManagerInterface $printable_entity_manager) {
    $this->printableEntityManager = $printable_entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RoutingEvents::ALTER] = 'routes';
    return $events;
  }

  /**
   * Adds a print route for each content entity.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The route build event.
   */
  public function routes(RouteBuildEvent $event) {
    $collection = $event->getRouteCollection();
    foreach ($this->printableEntityManager->getPrintableEntities() as $entity_type => $entity_definition) {
      $route = new Route(
        "/printable/{printable_format}/$entity_type/{entity}",
        array(
          '_controller' => 'Drupal\printable\Controller\PrintableController::showFormat',
          '_title' => 'Printable',
        ),
        array(
          // '_entity_access' => 'entity.view',
          '_permission' => 'view printer friendly versions',
        ),
        array(
          'parameters' => array(
            'entity' => array('type' => 'entity:' . $entity_type),
          ),
        )
      );
      $collection->add('printable.show_format.' . $entity_type, $route);
    }
  }

}
