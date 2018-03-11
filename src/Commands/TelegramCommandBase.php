<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 14.01.2018
 * Time: 22:50
 */

namespace Drupal\telegram_bots_api\Commands;


use Telegram\Bot\Commands\Command;

abstract class TelegramCommandBase extends Command {

  /**
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;

  public function __construct() {
    $this->logger = \Drupal::logger('telegram');
  }
}