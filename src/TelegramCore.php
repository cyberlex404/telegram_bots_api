<?php

namespace Drupal\telegram_bots_api;
use GuzzleHttp\Client;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class TelegramCore.
 */
class TelegramCore implements TelegramCoreInterface {

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;
  /**
   * Drupal\Core\Logger\LoggerChannel definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $loggerChannelTelegram;
  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * @var string
   */
  protected $token;
  /**
   * Constructs a new TelegramCore object.
   */
  public function __construct(Client $http_client, LoggerChannel $logger_channel_telegram, ConfigFactory $config_factory) {
    $this->httpClient = $http_client;
    $this->loggerChannelTelegram = $logger_channel_telegram;
    $this->configFactory = $config_factory;
  }


  public function setToken($token) {
    $this->token = $token;
    return $this;
  }

  protected function send() {

  }

  public function webhookInfo() {

  }

}
