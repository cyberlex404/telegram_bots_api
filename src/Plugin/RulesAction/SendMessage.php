<?php


namespace Drupal\telegram_bots_api\Plugin\RulesAction;


use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SendMessage' action.
 *
 * @RulesAction(
 *   id = "rules_telegram_bot_send_massage",
 *   label = @Translation("Send telegram message"),
 *   category = @Translation("Telegram"),
 *   context = {
 *     "bot" = @ContextDefinition("string",
 *       label = @Translation("Bot plugin ID"),
 *       description = @Translation("Plugin ID telegram Bot"),
 *       default_value = "default_notify_bot"
 *     ),
 *     "chat_id" = @ContextDefinition("string",
 *       label = @Translation("Chat ID"),
 *       description = @Translation("Unique identifier for the target chat or username of the target channel (in the format @channelusername)"),
 *     ),
 *     "message" = @ContextDefinition("string",
 *       label = @Translation("Message"),
 *       description = @Translation("Message"),
 *       default_value = @Translation("This message was sent by the module via Telegram bot.")
 *     )
 *   }
 * )
 */
class SendMessage extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\telegram_bots_api\TelegramBotsPluginManager definition.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $botsManager;
  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.telegrambots')
    );
  }

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param TelegramBotsPluginManager $botsManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $botsManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->botsManager = $botsManager;
  }



  protected function doExecute($bot, $chat_id, $message, $parse_mode = 'HTML') {
    $logger = \Drupal::logger('telegram action');

    /**
     * @var $telegramBot \Drupal\telegram_bots_api\TelegramBotInterface
     */
    $telegramBot = $this->botsManager->createInstance($bot);

    try{
      $message = $telegramBot->api()->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => $parse_mode,
      ]);
      if($message->getStatus()) {
        $logger->debug('status: ' . $message->getText());
      }
    }catch (\Exception $exception) {
      $logger->error($exception->getMessage());
    }

  }
}