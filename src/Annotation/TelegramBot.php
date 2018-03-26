<?php

namespace Drupal\telegram_bots_api\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a TelegramBot annotation object.
 *
 * Note that the "@ Annotation" line below is required and should be the last
 * line in the docblock. It's used for discovery of Annotation definitions.
 *
 * @see \Drupal\telegram_bots_api\TelegramBotsPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class TelegramBot extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * Human readable plugin label
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * The Bot Name
   * @var string
   */
  public $bot;

  /**
   * A brief, human readable, description of the sandwich type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The token for telegram bot
   *
   *
   * @var string
   */
  public $token;

  /**
   * Configurable flag
   *
   * @var bool
   */
  public $configurable;

  public function __construct($values) {
    $values += [
      'configurable' => FALSE,
    ];
    parent::__construct($values);
  }

}