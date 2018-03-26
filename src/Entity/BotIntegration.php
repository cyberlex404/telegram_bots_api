<?php

namespace Drupal\telegram_bots_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Url;
use Drupal\telegram_bots_api\Plugin\BotIntegrationPluginCollection;

/**
 * Defines the Bot integration entity.
 *
 * @ConfigEntityType(
 *   id = "bot_integration",
 *   label = @Translation("Bot integration"),
 *   handlers = {
 *     "list_builder" = "Drupal\telegram_bots_api\BotIntegrationListBuilder",
 *     "form" = {
 *       "add" = "Drupal\telegram_bots_api\Form\BotIntegrationForm",
 *       "edit" = "Drupal\telegram_bots_api\Form\BotIntegrationForm",
 *       "delete" = "Drupal\telegram_bots_api\Form\BotIntegrationDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\telegram_bots_api\BotIntegrationHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "bot_integration",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/services/bot_integration/add",
 *     "edit-form" = "/admin/config/services/bot_integration/{bot_integration}/edit",
 *     "delete-form" = "/admin/config/services/bot_integration/{bot_integration}/delete",
 *     "collection" = "/admin/config/services/bot_integration"
 *   }
 * )
 */
class BotIntegration extends ConfigEntityBase implements BotIntegrationInterface {

  /**
   * The Bot integration ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Bot integration label.
   *
   * @var string
   */
  protected $label;

  /**
   * @var boolean
   */
  protected $enabled;
  /**
   * The configuration of the plugin.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * The plugin ID of the integration Telegram Bot.
   *
   * @var string
   */
  protected $plugin;

  /**
   * Token
   * @var string
   */
  protected $token;

  /**
   * The plugin collection that stores Telegram Bot integration plugins.
   *
   * @var \Drupal\telegram_bots_api\Plugin\BotIntegrationPluginCollection
   */
  protected $pluginCollection;

  /**
   * Encapsulates the creation of the bot integration's LazyPluginCollection.
   *
   * @return \Drupal\Component\Plugin\LazyPluginCollection
   *   The bot integration's plugin collection.
   */
  /*
  protected function getPluginCollection() {
    if (!$this->pluginCollection) {
      $this->pluginCollection = new BotIntegrationPluginCollection(\Drupal::service('plugin.manager.telegrambots'), $this->plugin, $this->configuration);
    }
    return $this->pluginCollection;
  }
*/
  /**
   * {@inheritdoc}
   */
  /*
  public function getPluginCollections() {
    return ['configuration' => $this->getPluginCollection()];
  }
*/
  /**
   * {@inheritdoc}
   */
  /*
  public function getPlugin() {
    return $this->getPluginCollection()->get($this->plugin);
  }
*/
  public function getPlugin() {
    // TODO: Implement getPlugin() method.
  }


  /**
   * {@inheritdoc}
   */
  public function plugin() {
    return $this->plugin;
  }

  /**
   * @deprecated
   */
  public function enabled() {
    return $this->status();
  }

  /**
   * {@inheritdoc}
   */
  public function token() {
    return $this->token;
  }

  /**
   * {@inheritdoc}
   */
  public function webhookUrl() {
    return Url::fromRoute('telegram_bots_api.webhook', [
      'token' => $this->token(),
      'plugin' => $this->id(),
    ])->setAbsolute();
  }


}
