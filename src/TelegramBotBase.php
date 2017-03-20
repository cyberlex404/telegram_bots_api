<?php

namespace Drupal\telegram_bots_api;

use Drupal\Component\Plugin\PluginBase;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class TelegramBotBase extends PluginBase implements TelegramBotInterface {

  /**
   * @var \Telegram\Bot\Api
   */
  protected $api;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->api = new Api($this->token());
    $this->api->addCommands($this->commands());
  }


  public function bot() {
    return $this->pluginDefinition['bot'];
  }

  /**
   * {@inheritdoc}
   */
  public function description() {
    // Retrieve the @description property from the annotation and return it.
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function token() {
    return $this->pluginDefinition['token'];
  }

  /**
   * {@inheritdoc}
   */
  public function api() {
    return $this->api;
  }

  /**
   * {@inheritdoc}
   */
  final public function webhook() {
    try {

      $update = $this->api()->commandsHandler(true);
      if($update->getUpdateId()) {
        $this->executeUpdate($update);

        $info = [
          'Update ID:' . $update->getUpdateId(),
          'Text: ' . $update->getMessage()->getText(),
          'User: ' . $update->getMessage()->getFrom()->getUsername(),
          'User ID: ' . $update->getMessage()->getFrom()->getId(),
        ];
        $message = implode(" ", $info);
        \Drupal::logger('telegram_bot_api  final')->info($message);
      }

    } catch (\Telegram\Bot\Exceptions\TelegramResponseException $e) {
      \Drupal::logger('telegram_bot_api')->error($e->getMessage());
    }
  }

  abstract public function executeUpdate(Update $update);

  abstract public function commands();

}