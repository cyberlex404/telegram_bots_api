<?php

namespace Drupal\telegram_bots_api\Plugin;

use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Provides a container for lazily loading Telegram Bot plugins.
 */
class BotIntegrationPluginCollection extends DefaultSingleLazyPluginCollection {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\telegram_bots_api\TelegramBotInterface
   */
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

}