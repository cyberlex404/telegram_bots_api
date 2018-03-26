<?php

namespace Drupal\telegram_bots_api\Plugin\TelegramBots;

use Drupal\telegram_bots_api\TelegramBotBase;
use Telegram\Bot\Objects\Update;

/**
 * @TelegramBot(
 *   id = "cyberlex404_bot",
 *   label = @Translation("Cyberlex404 custom plugin"),
 *   description = @Translation("CyberLex404 Bot"),
 *   configurable = TRUE
 * )
 */

class Cyberlex404Bot extends TelegramBotBase {


  /**
   * @inheritdoc
   */
  public function commands() {
    $commands = parent::defaultCommands();
    $commands[] = \Drupal\telegram_bots_api\Commands\NotifyCommand::class;
    return $commands;
  }


  public function executeUpdate(Update $update) {
    // TODO: Implement executeUpdate() method.
    dpm($update);
  }

}