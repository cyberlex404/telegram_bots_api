<?php

namespace Drupal\telegram_bots_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BotConfigForm.
 */
class BotConfigForm extends ConfigFormBase {

  /**
   * Drupal\telegram_bots_api\TelegramBotsPluginManager definition.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $pluginManagerTelegrambots;
  /**
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;
  /**
   * Constructs a new BotConfigForm object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
      TelegramBotsPluginManager $plugin_manager_telegrambots,
    Renderer $renderer
    ) {
    parent::__construct($config_factory);
        $this->pluginManagerTelegrambots = $plugin_manager_telegrambots;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.telegrambots'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'telegram_bots_api.botconfig',
      'telegram_bots_api.bot.*',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bot_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $telegram_bot_plugin = NULL) {

    dpm($telegram_bot_plugin);
    $config = $this->config('telegram_bots_api.bot.' . $telegram_bot_plugin);

    $form['bot_plugin'] = [
      '#type' => 'hidden',
      '#value' => $telegram_bot_plugin,
    ];
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#description' => $this->t('Telegram bot token'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $config->get('token'),
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('bot'),
    ];

    $form['actions']['web_hook'] = [
      '#type' => 'submit',
      '#value' => $this->t('Set webHook'),
      '#submit' => [$this, 'setWebHook'],
    ];
    return parent::buildForm($form, $form_state);
  }


  public function setWebHook(array &$form, FormStateInterface $form_state) {
    dpm($form_state->getValues());
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
    parent::submitForm($form, $form_state);

    $telegram_bot_plugin = $form_state->getValue('bot_plugin');

    $this->config('telegram_bots_api.bot.' . $telegram_bot_plugin)
      ->set('token', $form_state->getValue('token'))
      ->set('bot', $form_state->getValue('name'))
      ->save();
  }

}
