<?php

namespace Drupal\telegram_bots_api;

use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class TelegramApi extends Api {


  /**
   * Api block
   */

  public function webhookInfo() {
    $response = $this->post('getWebhookInfo');
    return new Message($response->getDecodedBody());
  }


  public function getWebhookUpdates(Request $request = NULL) {
    if ($request === NULL) {
      \Drupal::logger('telegram')->error('use php-input');
      return parent::getWebhookUpdates();
    }
    $body = Json::decode($request->getContent());
    \Drupal::logger('telegram')->debug(Json::encode($body));
    return new Update($body);
  }

  /**
   * {@inheritdoc}
   */
  public function commandsHandler($webhook = false, Request $request = NULL) {
    if ($webhook) {
      $update = $this->getWebhookUpdates($request);
      $this->processCommand($update);

      return $update;
    }

    $updates = $this->getUpdates();
    $highestId = -1;

    foreach ($updates as $update) {
      $highestId = $update->getUpdateId();
      $this->processCommand($update);
    }

    //An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id.
    if ($highestId != -1) {
      $params = [];
      $params['offset'] = $highestId + 1;
      $params['limit'] = 1;
      $this->getUpdates($params);
    }

    return $updates;
  }
}