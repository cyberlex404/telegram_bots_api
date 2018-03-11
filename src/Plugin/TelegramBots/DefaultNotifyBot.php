<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 13.01.2018
 * Time: 13:25
 */

namespace Drupal\telegram_bots_api\Plugin\TelegramBots;

use Drupal\Component\Serialization\Json;
use Drupal\telegram_bots_api\TelegramBotBase;
use Telegram\Bot\Objects\Update;


/**
 * @TelegramBot(
 *   id = "default_notify_bot",
 *   description = @Translation("Default notify Bot"),
 *   configurable = TRUE
 * )
 */

class DefaultNotifyBot extends TelegramBotBase {

  public function executeUpdate(Update $update) {
    \Drupal::logger('telegram')->debug(Json::encode($update));
  }

  /**
   * @inheritdoc
   */
  public function commands() {
    $commands = parent::defaultCommands();
    $commands[] = \Drupal\telegram_bots_api\Commands\NotifyCommand::class;
    $commands[] = \Drupal\telegram_bots_api\Commands\SubscribeCommand::class;
    return $commands;
  }

}