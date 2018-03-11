<?php

namespace Drupal\telegram_bots_api\Plugin\WebformHandler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\telegram_bots_api\TelegramBotsPluginManager;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\Plugin\WebformHandlerMessageInterface;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformThemeManagerInterface;
use Drupal\webform\WebformTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Emails a webform submission.
 *
 * @WebformHandler(
 *   id = "telegram",
 *   label = @Translation("Telegram bot"),
 *   category = @Translation("Notification"),
 *   description = @Translation("Sends a webform submission via an telegram bot."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class TelegramBotWebformHandler extends WebformHandlerBase implements WebformHandlerMessageInterface {
  /**
   * Other option value.
   */
  const OTHER_OPTION = '_other_';

  /**
   * Default option value.
   */
  const EMPTY_OPTION = '_empty_';

  /**
   * Default option value.
   */
  const DEFAULT_OPTION = '_default_';

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * A mail manager for sending email.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The webform token manager.
   *
   * @var \Drupal\webform\WebformTokenManagerInterface
   */
  protected $tokenManager;

  /**
   * The webform theme manager.
   *
   * @var \Drupal\webform\WebformThemeManagerInterface
   */
  protected $themeManager;

  /**
   * A webform element plugin manager.
   *
   * @var \Drupal\webform\Plugin\WebformElementManagerInterface
   */
  protected $elementManager;

  /**
   * Cache of default configuration values.
   *
   * @var array
   */
  protected $defaultValues;

  /**
   * The TelegramBotsPluginManager.
   *
   * @var \Drupal\telegram_bots_api\TelegramBotsPluginManager
   */
  protected $telegramBotsManager;

  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              LoggerChannelFactoryInterface $logger_factory,
                              ConfigFactoryInterface $config_factory,
                              EntityTypeManagerInterface $entity_type_manager,
                              WebformSubmissionConditionsValidatorInterface $conditions_validator,
                              AccountInterface $current_user,
                              ModuleHandlerInterface $module_handler,
                              LanguageManagerInterface $language_manager,
                              MailManagerInterface $mail_manager,
                              WebformThemeManagerInterface $theme_manager,
                              WebformTokenManagerInterface $token_manager,
                              WebformElementManagerInterface $element_manager,
                              TelegramBotsPluginManager $telegramBotsManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger_factory, $config_factory, $entity_type_manager, $conditions_validator);

    $this->currentUser = $current_user;
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
    $this->mailManager = $mail_manager;
    $this->themeManager = $theme_manager;
    $this->tokenManager = $token_manager;
    $this->elementManager = $element_manager;
    $this->telegramBotsManager = $telegramBotsManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('webform_submission.conditions_validator'),
      $container->get('current_user'),
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('plugin.manager.mail'),
      $container->get('webform.theme_manager'),
      $container->get('webform.token_manager'),
      $container->get('plugin.manager.webform.element'),
      $container->get('plugin.manager.telegrambots')
    );
  }

  /**
   * @inheritdoc
   */
  public function defaultConfiguration() {
    return [
      'states' => [WebformSubmissionInterface::STATE_COMPLETED],
      'bot_plugin' => 'default_notify_bot',
      'chat_id' => '205684384',
    ];
  }


  public function getMessage(WebformSubmissionInterface $webform_submission) {
    // TODO: Implement getMessage() method.
    $message = [];
    $token_options = [
      'email' => TRUE,
      'excluded_elements' => $this->configuration['excluded_elements'],
      'ignore_access' => $this->configuration['ignore_access'],
      'exclude_empty' => $this->configuration['exclude_empty'],
      'html' => ($this->configuration['html'] && $this->supportsHtml()),
    ];

    $token_data = [];
    return $message;
  }

  public function sendMessage(WebformSubmissionInterface $webform_submission, array $message) {
    $botPluginId = $this->configuration['bot_plugin'];
    $chat_id = $this->configuration['chat_id'];
    /**
     * @var $telegramBot \Drupal\telegram_bots_api\TelegramBotInterface
     */
    $telegramBot = $this->telegramBotsManager->createInstance($botPluginId);

    $message = $telegramBot->api()->sendMessage([
      'chat_id' => $chat_id,
      'text' => 'Demo text',
      'parse_mode' => 'HTML',
    ]);
    \Drupal::logger('telegram')->debug($message->getMessageId());
    // TODO: Implement sendMessage() method.
  }

  public function hasRecipient(WebformSubmissionInterface $webform_submission, array $message) {
    // TODO: Implement hasRecipient() method.
  }

  public function resendMessageForm(array $message) {
    $element = [];
    return $element;
    // TODO: Implement resendMessageForm() method.
  }

  public function getMessageSummary(array $message) {
    return [
        '#settings' => $message,
      ] + parent::getSummary();
  }


}