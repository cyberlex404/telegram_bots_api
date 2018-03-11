<?php


namespace Drupal\telegram_bots_api\Exceptions;


use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramTokenException extends TelegramSDKException {

  public function __construct() {
    $message = "No token specified";
    parent::__construct($message);
  }
}