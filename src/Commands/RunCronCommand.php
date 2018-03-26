<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 13.03.2018
 * Time: 6:18
 */

namespace Drupal\telegram_bots_api\Commands;

use Telegram\Bot\Actions;

class RunCronCommand extends TelegramCommandBase{

  /**
   * @var string Command Name
   */
  protected $name = "cron";

  /**
   * @var string Command Description
   */
  protected $description = "Run cron";

  public function handle($arguments) {
    $this->replyWithChatAction(['action' => Actions::TYPING]);
    /**
     * @var \Drupal\Core\CronInterface
     */
    $cron = \Drupal::service('cron');
    $success = $cron->run();
    if ($success) {
      $this->replyWithMessage(['text' => 'Cron was successfully launched']);
    }else{
      $this->replyWithMessage(['text' => 'Error launching cron']);
    }
  }


}