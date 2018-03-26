<?php

namespace Drupal\telegram_bots_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Bot integration entities.
 */
interface BotIntegrationInterface extends ConfigEntityInterface {

  /**
   * Returns the telegram bot plugin.
   *
   * @return \Drupal\telegram_bots_api\TelegramBotInterface
   */
  public function getPlugin();

  /**
   * Return Telegram bot plugin id
   *
   * @return string
   */
  public function plugin();

  /**
   * @return string
   */
  public function token();

  /**
   * @return \Drupal\Core\Url
   */
  public function webhookUrl();
}
