<?php

namespace Drupal\telegram_bots_api;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\telegram_bots_api\Annotation\TelegramBot;

class TelegramBotsPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {

    $subdir = 'Plugin/TelegramBots';
    $plugin_interface = TelegramBotInterface::class;
    $plugin_definition_annotation_name = TelegramBot::class;

    parent::__construct($subdir, $namespaces, $module_handler, $plugin_interface, $plugin_definition_annotation_name);
    $this->alterInfo('telegram_bots_info');
    $this->setCacheBackend($cache_backend, 'telegram_bots_plugins');
  }
}