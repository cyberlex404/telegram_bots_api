<?php

namespace Drupal\telegram_bots_api\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\telegram_bots_api\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {


  /**
   * The entity type manager service.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;

  /**
   * Constructs a new RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity type manager.
   */
  public function __construct(TelegramBotsPluginManager $telegramBotsManager) {
    $this->telegramBotsManager = $telegramBotsManager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $telegramBotsDefinitions = $this->telegramBotsManager->getDefinitions();

   // dpm($telegramBotsDefinitions, '$telegramBotsDefinitions');

    foreach ($telegramBotsDefinitions as $botsDefinition) {

      $token = $botsDefinition['token'];
      $route = new Route(
      // the url path to match
        'telegram/webhook/' . $token,
        // the defaults (see the trousers.routing.yml for structure)
        array(
          '_title' => $botsDefinition['bot'],
          '_controller' => '\Drupal\telegram_bots_api\Controller\Webhook::webhook',
          'bot' => $botsDefinition['id'],
          'telegram_token' => $token,
        ),
        // the requirements
        array(
          '_permission' => 'access content',
        )
      );
      $route->setMethods(['POST', 'GET']); // todo : remove GET method
      // Add our route to the collection with a unique key.
      $collection->add('telegram_bots_api.webhook.' . $token, $route);
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', 100];
    return $events;
  }
}