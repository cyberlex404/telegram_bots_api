<?php

namespace Drupal\telegram_bots_api\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Url;
use Drupal\telegram_bots_api\Entity\BotIntegrationInterface;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BotIntegrationForm.
 */
class BotIntegrationForm extends EntityForm {

  /**
   * Entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The telegram bots manager.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $botManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('logger.channel.telegram'),
      $container->get('plugin.manager.telegrambots')
    );
  }

  /**
   * Constructs a CommentTypeFormController
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager service.
   * @param LoggerChannelInterface $logger
   *   A logger instance.
   * @param TelegramBotsPluginManager $botManager
   *   The comment manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelInterface $logger, TelegramBotsPluginManager $botManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->logger = $logger;
    $this->botManager = $botManager;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /**
     * @var $bot_integration \Drupal\telegram_bots_api\Entity\BotIntegration
     */
    $bot_integration = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $bot_integration->label(),
      '#description' => $this->t("Label for the Bot integration."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $bot_integration->id(),
      '#machine_name' => [
        'exists' => '\Drupal\telegram_bots_api\Entity\BotIntegration::load',
      ],
      '#disabled' => !$bot_integration->isNew(),
    ];

    $form['plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Plugin'),
      '#options' => $this->pluginOptions(),
      '#default_value' => $bot_integration->plugin(),
      '#description' => $this->t("Label for the Bot integration."),
      '#required' => TRUE,
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled status'),
      '#default_value' => $bot_integration->status(),
    ];

    $url = Url::fromUri('https://telegram.me/botfather', [
      'attributes' => ['target' => '_blank'],
    ]);
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#maxlength' => 255,
      '#default_value' => $bot_integration->token(),
      '#description' => $this->t("Enter token: e.g. %example @gettoken", [
        '%example' => '501785948:AAEEVb00xKgTb7arMpc8_nZQBNy1S0QGiBQ',
        '@gettoken' => Link::fromTextAndUrl($this->t('Get a token from a BotFather'), $url)->toString()
      ]),
      '#required' => TRUE,
    ];


    $form['webhook'] = [
      '#type' => 'submit',
      '#submit' => ['::setWebHook'],
      '#value' => $this->t('setWebHook'),
    ];
    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $bot_integration = $this->entity;
    $status = $bot_integration->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Bot integration.', [
          '%label' => $bot_integration->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Bot integration.', [
          '%label' => $bot_integration->label(),
        ]));
    }
    $form_state->setRedirectUrl($bot_integration->toUrl('collection'));
  }


  protected function pluginOptions() {
    $plugin_definitions = $this->botManager->getDefinitions();

    $options = [];
    foreach ($plugin_definitions as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = $plugin_definition['description'];
    }
    return $options;
  }

  public function setWebHook(array &$form, FormStateInterface $form_state) {
    $bot_integration = $this->entity;
    dpm($bot_integration);



    $demo = '453816184:AAGy7zCkraMUiiH5UgcT3Y8pnZZCd5X0Drk';
    if ($bot_integration instanceof BotIntegrationInterface) {
      $demo = $bot_integration->token();
      $telegram_bot_plugin = $bot_integration->plugin();
    }

    /**
     * @var $telegramBot \Drupal\telegram_bots_api\TelegramBotInterface
     */
    try{
      $telegramBot = $this->botManager->createInstance($telegram_bot_plugin, ['token' => $demo]);
    }catch (\Exception $exception) {
      drupal_set_message($exception->getMessage(), 'error');
      return;
    }


    if ($bot_integration instanceof BotIntegrationInterface) {
      $telegramBot->setWebHook($bot_integration->webhookUrl());
    }


    dpm($telegramBot->token());
  }

}
