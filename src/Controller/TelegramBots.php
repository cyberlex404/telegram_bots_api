<?php

namespace Drupal\telegram_bots_api\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\telegram_bots_api\TelegramBotInterface;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Drupal\telegram_bots_api\TelegramCore;
use Masterminds\HTML5\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TelegramBots.
 *
 * @package Drupal\telegram_bots_api\Controller
 */
class TelegramBots extends ControllerBase {

  /**
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * @var \Drupal\telegram_bots_api\TelegramCore
   */
  protected $api;

  /**
   * The TelegramBotsPluginManager.
   *
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;

  function __construct(TelegramBotsPluginManager $telegramBotsManager, Renderer $renderer, TelegramCore $api) {
    $this->telegramBotsManager = $telegramBotsManager;
    $this->renderer = $renderer;
    $this->api = $api;
  }
  /**
   * {@inheritdoc}
   *
   * @see container
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.telegrambots'),
      $container->get('renderer'),
      $container->get('telegram_bots_api.coreapi') // @todo remove this
    );
  }

  /**
   * List.
   *
   * @return array
   */
  public function list() {

    $build = [];

    $build['intro'] = array(
      '#markup' => t("This page lists the Telegram bots plugins."),
    );

    $header = [
      'plugin' => $this->t('Plugin'),
      'name' => $this->t('Bot name'),
      'token' => $this->t('Token'),
      'links' => $this->t('Link')
    ];

    $rows = [];

    $plugin_definitions = $this->telegramBotsManager->getDefinitions();

    foreach ($plugin_definitions as $plugin_id => $plugin_definition) {
      /**
       * @var $bot TelegramBotInterface
       */
      $bot = $this->telegramBotsManager->createInstance($plugin_id);


      $coreApi = $this->api->setToken($bot->token()); // @todo remove this

      $links = [
        '#type' => 'dropbutton',
        '#links' => [
          'config' => [
            'title' => $this->t('Config'),
            'url' => Url::fromRoute('telegram_bots_api.bot_config_form', [
              'telegram_bot_plugin' => $plugin_id,
            ]),
          ],
          'test' => [
            'title' => $this->t('Test'),
            'url' => Url::fromRoute('telegram_bots_api.bot_config_form', [
              'telegram_bot_plugin' => $plugin_id,
            ]),
          ],
        ],
      ];

      try {
        $linkOut = $this->renderer->render($links);
      }catch (\Exception $e) {
        $linkOut = '';
      }

      $token = $bot->token();
      $rows[] = [
        'plugin' => $plugin_id,
        'name' => $plugin_id,
        'token' => $token,
        'links' => $linkOut,
      ];
    }

    // Add our list to the render array.
    $build['plugin_definitions'] = array(
      '#theme' => 'table',
      //'#cache' => ['disabled' => TRUE],
    //  '#caption' => 'Bot list',
      '#header' => $header,
      '#rows' => $rows,
    );

    $header = [
      'plugin' => $this->t('Plugin'),
      'name' => $this->t('Bot name'),
      'token' => $this->t('Token'),
      'links' => $this->t('Link')
    ];
    $rows = [];

    try{
      $integrationStorage = $this->entityTypeManager()->getStorage('bot_integration');

      $integrations = $integrationStorage->loadMultiple();
      /**
       * @var $integrations \Drupal\telegram_bots_api\Entity\BotIntegrationInterface[]
       */
      foreach ($integrations as $integration) {
        $rows[] = [
          'plugin' => $integration->plugin(),
          'token' => $integration->token(),
        ];
      }
    }catch (InvalidPluginDefinitionException $exception) {

    }




    return $build;
  }

}
