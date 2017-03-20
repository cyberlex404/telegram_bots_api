<?php

namespace Drupal\telegram_bots_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\telegram_bots_api\TelegramBotInterface;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TelegramBots.
 *
 * @package Drupal\telegram_bots_api\Controller
 */
class TelegramBots extends ControllerBase {

  /**
   * The TelegramBotsPluginManager.
   *
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;

  function __construct(TelegramBotsPluginManager $telegramBotsManager) {
    $this->telegramBotsManager = $telegramBotsManager;
  }
  /**
   * {@inheritdoc}
   *
   *
   * @see container
   */
  public static function create(ContainerInterface $container) {
    // Inject the plugin.manager.sandwich service that represents our plugin
    // manager as defined in the plugin_type_example.services.yml file.
    return new static($container->get('plugin.manager.telegrambots'));
  }

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function list() {

    $build = array();

    $build['intro'] = array(
      '#markup' => t("This page lists the Telegram bots plugins we've created. The Telegram bots plugin type is defined in Drupal\\telegram_bots_api\\TelegramBots. The various plugins are defined in the Drupal\\telegram_bots_api\\Plugin\\TelegramBots namespace."),
    );

    // Get the list of all the sandwich plugins defined on the system from the
    // plugin manager. Note that at this point, what we have is *definitions* of
    // plugins, not the plugins themselves.
    $sandwich_plugin_definitions = $this->telegramBotsManager->getDefinitions();

    // Let's output a list of the plugin definitions we now have.
    $items = array();
    foreach ($sandwich_plugin_definitions as $sandwich_plugin_definition) {
      // Here we use various properties from the plugin definition. These values
      // are defined in the annotation at the top of the plugin class: see
      // \Drupal\plugin_type_example\Plugin\Sandwich\ExampleHamSandwich.
      $items[] = t("@id (calories: @calories, description: @description )", array(
        '@id' => $sandwich_plugin_definition['id'],
        '@calories' => $sandwich_plugin_definition['calories'],
        '@description' => $sandwich_plugin_definition['description'],
      ));
    }

    // Add our list to the render array.
    $build['plugin_definitions'] = array(
      '#theme' => 'item_list',
      '#title' => 'Sandwich plugin definitions',
      '#items' => $items,
    );


    $items = array();
    // The array of plugin definitions is keyed by plugin id, so we can just use
    // that to load our plugin instances.
    foreach ($sandwich_plugin_definitions as $plugin_id => $sandwich_plugin_definition) {


      /**
       * @var $telegramBot TelegramBotInterface
       */
      $telegramBot = $this->telegramBotsManager->createInstance($plugin_id, array('of' => 'configuration values'));

      dpm($telegramBot->token(),$telegramBot->bot());

      $items[] = $telegramBot->description();
      $telegramBot->webhook();
    }

    $build['plugins'] = array(
      '#theme' => 'item_list',
      '#title' => 'Sandwich plugins',
      '#items' => $items,
    );
    return $build;
  }

}
