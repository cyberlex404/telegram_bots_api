<?php

namespace Drupal\telegram_bots_api\Plugin\TelegramBots;

use Drupal\Component\Serialization\Json;
use Drupal\telegram_bots_api\TelegramBotBase;
use Telegram\Bot\Objects\Update;

/**
 * @TelegramBot(
 *   id = "cyberlex404test_bot",
 *   description = @Translation("CyberLex404 Test 2 Bot"),
 *   configurable = TRUE
 * )
 */

class Cyberlex404BotTest extends TelegramBotBase {

  /**
   * @inheritdoc
   */
  public function commands() {
    $commands = parent::defaultCommands();
    $commands[] = \Drupal\telegram_bots_api\Commands\NotifyCommand::class;
    return $commands;
  }


  public function executeUpdate(Update $update) {
    \Drupal::logger('telegram webhook')->debug(Json::encode($update));
  }

}