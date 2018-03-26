<?php

namespace Drupal\telegram_bots_api;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\telegram_bots_api\Exceptions\TelegramTokenException;
use Drupal\telegram_bots_api\TelegramApi as Api;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use Telegram\Bot\Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Objects\Update;
use Zend\Diactoros\Response\JsonResponse;

abstract class TelegramBotBase extends PluginBase implements TelegramBotInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;
  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Drupal\Core\Logger\LoggerChannel definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;

  /**
   * @var array
   */
  protected $botInfo;

  /**
   * @var string
   */
  protected $token;

  /**
   * @var \Telegram\Bot\Api
   */
  protected $api;


  /**
   * @var \Drupal\telegram_bots_api\Entity\BotIntegrationInterface
   */
  protected $integration;


  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerChannel $logger, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->token = $configuration['token'];
    $this->logger = $logger;
    $this->configFactory = $config_factory;
    /**
     * @todo add set $integration value
     */
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.channel.telegram'),
      $container->get('config.factory')
    );
  }


  public function id() {
    return $this->pluginDefinition['id'];
  }

  public function label() {
    return $this->pluginDefinition['label'];
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
   // dpm($this->token, 'token()');
   // dpm((int) $this->isConfigurable(), 'conf');
    if(!$this->token) {
      if ($this->isConfigurable()) {
        $config = $this->getConfig();
        $this->token = $config->get('token');
      }else{
        $this->token = $this->tokenDefinition();
      }
    }
    return $this->token;
  }

  public function tokenDefinition() {
    return $this->pluginDefinition['token'];
  }

  public function isConfigurable() {
    return $this->pluginDefinition['configurable'];
  }

  /**
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected function getConfig() {
    return $this->configFactory->get('telegram_bots_api.bot.' . $this->id());
  }

  /**
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  public function getEditableConfig() {
    return $this->configFactory->getEditable('telegram_bots_api.bot.' . $this->id());
  }


  /**
   * {@inheritdoc}
   */
  public function api() {
    if (!$this->api) {

      $token = $this->token();
      if (empty($token)) {
        throw new TelegramTokenException();
      }

      $this->api = new Api($token);
      $this->api->addCommands($this->commands());
    }
    return $this->api;
  }


  /**
   * @todo Replace by $this->integration->webhookUrl()
   * @param \Drupal\Core\Url $url
   */
  public function setWebHook(Url $url) {
    try{
      $api = $this->api();
     // $url->setAbsolute();
      $response = $api->setWebhook(['url' => $url->toString()]);


      dpm($response, $url->toString());


    }catch (\Exception $e) {

      drupal_set_message($e->getMessage(), 'error');
    }
  }

  /**
   * {@inheritdoc}
   */
  final public function webHook(Request $request) {

    try {
      $api = $this->api();
    }catch (\Exception $exception) {
      return new JsonResponse(['status' => 'error']);
    }

    try {

      $update = $api->commandsHandler(true, $request);
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

    } catch (TelegramResponseException $e) {
      $this->apiLogger()->error($e->getMessage());
    }
    $response = new JsonResponse(['updateID' => $update->getUpdateId(), 'met' => $request->getMethod()]);
    return $response;
  }

  protected function apiLogger() {
    return \Drupal::logger('telegram_bot_api');
  }

  public function getBotInfo() {

    if(!$this->botInfo) {

      $this->botInfo['token'] = '';
    }

    return $this->botInfo;
  }


  protected function defaultCommands() {
    return [
      \Telegram\Bot\Commands\HelpCommand::class,
      \Drupal\telegram_bots_api\Commands\StartCommand::class,
      \Drupal\telegram_bots_api\Commands\RunCronCommand::class,
      \Drupal\telegram_bots_api\Commands\GetmeCommand::class,
    ];
  }

  abstract public function commands();

  abstract public function executeUpdate(Update $update);



  public function webhookInfo() {
    $message = $this->api()->webhookInfo();
    dpm($message);
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $definition = $this->getPluginDefinition();
    $form['hello'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hello message'),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $form += $this->settingsForm($form, $form_state);
    return $form;
  }

  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Process the block's submission handling if no errors occurred only.
    if (!$form_state->getErrors()) {
      $this->configuration['hello'] = $form_state->getValue('hello');
      $this->settingsSubmit($form, $form_state);
    }
  }
  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSubmit($form, FormStateInterface $form_state) {}



}