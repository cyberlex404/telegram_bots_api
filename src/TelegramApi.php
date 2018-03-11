<?php

namespace Drupal\telegram_bots_api;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class TelegramApi extends Api {


  /**
   * Api block
   */

  public function webhookInfo() {
    $response = $this->post('getWebhookInfo');
    return new Message($response->getDecodedBody());
  }
}