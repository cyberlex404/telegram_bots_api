<?php

namespace Drupal\telegram_bots_api;

use Drupal\Component\Plugin\PluginBase;

abstract class TelegramBotBase extends PluginBase implements TelegramBotInterface {


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
  abstract public function webhook($update);

}