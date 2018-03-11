<?php

namespace Drupal\telegram_bots_api\Controller;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Logger\LoggerChannel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Telegram\Bot\Api;
use Drupal\telegram_bots_api\TelegramBotInterface;

/**
 * Class Webhook.
 *
 * @package Drupal\telegram_bots_api\Controller
 */
class Webhook extends ControllerBase {

  /**
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;
  /**
   * @var Api
   */
  protected $telegram;

  /**
   * The TelegramBotsPluginManager.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;

  /**
   * {@inheritdoc}
   */
  public function __construct($entity_type_manager,
                              LoggerChannel $logger,
                              $telegramBotsManager) {
    /**
     * @var $config_factory ConfigFactoryInterface
     */
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
    $this->telegramBotsManager = $telegramBotsManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('logger.channel.telegram'),
      $container->get('plugin.manager.telegrambots')
    );
  }


  /**
   * Plugin ID
   *
   * @param $bot
   *
   * @return JsonResponse
   */
  public function webHookR($bot, $telegram_token) {
    /**
     * @var $telegramBot TelegramBotInterface
     */
    $telegramBot = $this->telegramBotsManager->createInstance($bot);

    dpm($telegram_token);
    if ($telegramBot instanceof TelegramBotInterface) {
      $response = $telegramBot->webHook();
    }

    return $response;
  }

  public function webHook($token, $plugin) {
    $response = new JsonResponse([]);

    try{
      /**
       * @var $telegramBot TelegramBotInterface
       */
      $telegramBot = $this->telegramBotsManager->createInstance($plugin);

      if ($telegramBot instanceof TelegramBotInterface) {
        $response = $telegramBot->webHook();
      }
    }catch (PluginException $exception) {
      $this->logger->error("Bot: $plugin. Error:" . $exception->getMessage());
      return $response;
    }
    return $response;
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access($bot, $telegram_token) {
    $telegramBot = $this->telegramBotsManager->createInstance($bot);
    if ($telegramBot instanceof TelegramBotInterface) {
      if ($telegramBot->token() == $telegram_token) {
        return AccessResult::allowed();
      }else {
        return AccessResult::forbidden();
      }
    }else {
      return AccessResult::forbidden();
    }

  }

}
