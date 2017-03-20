<?php

namespace Drupal\telegram_bots_api\Plugin\TelegramBots;

use Drupal\telegram_bots_api\TelegramBotBase;
use Telegram\Bot\Objects\Update;

/**
 * @TelegramBot(
 *   id = "CyberLex404Bot",
 *   bot = "CyberLex404Bot",
 *   description = @Translation("CyberLex404 Bot"),
 *   token = "331814260:AAFiyqskNb7oW_t7s_jEN-zT4v4t2D5otJY"
 * )
 */

class Cyberlex404Bot extends TelegramBotBase {

  public function commands() {
    return [
      \Telegram\Bot\Commands\HelpCommand::class,
      \Drupal\telegram_holiday\Commands\StartCommand::class,
    ];
  }


  public function executeUpdate(Update $update) {
    // TODO: Implement executeUpdate() method.
    dpm($update);
  }

}