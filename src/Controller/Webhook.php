<?php

namespace Drupal\telegram_bots_api\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\telegram_bots_api\Entity\BotIntegrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
   * @deprecated 1.x-dev
   * @return JsonResponse
   */
  public function webHookR($bot, $telegram_token) {
    /**
     * @var $telegramBot TelegramBotInterface
     * @var $integration \Drupal\telegram_bots_api\Entity\BotIntegrationInterface
     */
    try{
      $integration = $this->entityTypeManager()->getStorage('bot_integration')->load($bot);
    }catch (InvalidPluginDefinitionException $exception) {
      \Drupal::logger('telegram')->error('Error in load intagration');
    }


    $telegramBot = $this->telegramBotsManager->createInstance($bot);

    dpm($telegram_token);
    if ($telegramBot instanceof TelegramBotInterface) {
      $response = $telegramBot->webHook(\Drupal::request());
    }

    return $response;
  }

  public function webHook($token, $plugin, Request $request) {
    $response = new JsonResponse(['getMethod' => $request->getMethod()]);
    dpm($token);
    dpm($plugin);

    try{
      /**
       * @var $integration \Drupal\telegram_bots_api\Entity\BotIntegrationInterface
       */
      $integration = $this->entityTypeManager()->getStorage('bot_integration')->load($plugin);
      dpm($integration);
    }catch (InvalidPluginDefinitionException $exception) {
      \Drupal::logger('telegram')->error('Error in load intagration');
      return [];
    }


    if ($integration instanceof BotIntegrationInterface) {
      $pluginId = $integration->plugin();
      $configuration = [
        'integration' => $integration,
        'token' => $integration->token(),
      ];
      try{
        $telegramBot = $this->telegramBotsManager->createInstance($pluginId, $configuration);
      }catch (PluginException $exception){
        $this->logger->error($exception->getMessage());
        return $response;
      }

      if ($telegramBot instanceof TelegramBotInterface) {
        $response = $telegramBot->webHook($request);
      }
    }

    if ($request->getMethod() == 'GET') {
      return ['#markup' => $integration->token()];
    }
    return $response;
  }

  /**
   * Checks access for a specific request.
   * @todo now not use
   * @todo fix in telegram_bots_api.routing.yml
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @return AccessResult
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
