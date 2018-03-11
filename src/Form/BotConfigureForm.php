<?php

namespace Drupal\telegram_bots_api\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Masterminds\HTML5\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Drupal\Core\Logger\LoggerChannel;

/**
 * Class BotConfigureForm.
 */
class BotConfigureForm extends FormBase {

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  /**
   * Drupal\telegram_bots_api\TelegramBotsPluginManager definition.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;
  /**
   * Drupal\Core\Logger\LoggerChannel definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $loggerChannelTelegram;
  /**
   * Constructs a new BotConfigureForm object.
   */
  public function __construct(
    ConfigFactory $config_factory,
    TelegramBotsPluginManager $plugin_manager_telegrambots,
    LoggerChannel $logger_channel_telegram
  ) {
    $this->configFactory = $config_factory;
    $this->telegramBotsManager = $plugin_manager_telegrambots;
    $this->loggerChannelTelegram = $logger_channel_telegram;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.telegrambots'),
      $container->get('logger.channel.telegram')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bot_configure_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $telegram_bot_plugin = NULL) {

    if (!$telegram_bot_plugin) {
      drupal_set_message('Error', 'error');
    }

    $form['bot_plugin'] = [
      '#type' => 'hidden',
      '#value' => $telegram_bot_plugin,
    ];

    $config = $this->configFactory->getEditable('telegram_bots_api.bot.' . $telegram_bot_plugin);

    $form['bot'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bot name'),
      '#maxlength' => 200,
      '#size' => 64,
      '#default_value' => $config->get('bot'),
      '#required' => TRUE,
    ];
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#maxlength' => 200,
      '#size' => 64,
      '#default_value' => $config->get('token'),
      '#required' => TRUE,
    ];
    //chat_id
    $form['chat_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Chat ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('chat_id'),
      '#required' => FALSE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['web_hook'] = [
      '#type' => 'submit',
      '#value' => $this->t('Set webHook'),
      '#submit' => ['::setWebHook'],
    ];

    $form['actions']['test'] = [
      '#type' => 'submit',
      '#value' => $this->t('TEST!'),
      '#submit' => ['::testBot'],
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save config'),
    ];

    return $form;
  }


  public function testBot(array &$form, FormStateInterface $form_state) {
    $telegram_bot_plugin = $form_state->getValue('bot_plugin');
    /**
     * @var $telegramBot \Drupal\telegram_bots_api\TelegramBotInterface
     */
    $telegramBot = $this->telegramBotsManager->createInstance($telegram_bot_plugin);

    $chat_id = $form_state->getValue('chat_id');
    if (!$chat_id) {
      drupal_set_message($this->t('Unknown chat ID'), 'error');
    }
    try{
      $api = $telegramBot->api();
      $message = $api->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Hello! This is test message: " . $telegram_bot_plugin,
      ]);
      dpm($message);
      dpm($message->getStatus());


      $this->logger('telegram')->debug(Json::encode($message));
    }catch (\Exception $e) {

    }

  }


  public function setWebHook(array &$form, FormStateInterface $form_state) {
    $telegram_bot_plugin = $form_state->getValue('bot_plugin');
    $config = $this->configFactory->getEditable('telegram_bots_api.bot.' . $telegram_bot_plugin);

    /**
     * @var $telegramBot \Drupal\telegram_bots_api\TelegramBotInterface
     */
    $telegramBot = $this->telegramBotsManager->createInstance($telegram_bot_plugin);

    $telegramBot->setWebHook();

    dpm($telegramBot->token());
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $telegram_bot_plugin = $form_state->getValue('bot_plugin');
    $config = $this->configFactory->getEditable('telegram_bots_api.bot.' . $telegram_bot_plugin);

    $config
      ->set('token', $form_state->getValue('token'))
      ->set('bot', $form_state->getValue('bot'))
      ->set('chat_id', $form_state->getValue('chat_id'))
      ->save();
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
