<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 09.03.2017
 * Time: 22:37
 */

namespace Drupal\telegram_bots_api\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class SubscribeCommand extends Command {

  /**
   * @var string Command Name
   */
  protected $name = "subscribe";

  /**
   * @var string Command Description
   */
  protected $description = "Подписка на уведомления\xE2\x9C\x89";


  /**
   * @inheritdoc
   */
  public function handle($arguments) {
    // This will update the chat status to typing...
    $this->replyWithChatAction(['action' => Actions::TYPING]);
    // todo: реализовать подписку на уведомления по заявке

    $update = $this->getUpdate();
    $fromid = $update->getMessage()->getFrom()->getId();

    $this->replyWithMessage(['text' => $fromid]);

  }
}