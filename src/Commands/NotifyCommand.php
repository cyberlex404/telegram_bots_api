<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 09.03.2017
 * Time: 22:37
 */

namespace Drupal\telegram_bots_api\Commands;

use Drupal\Component\Serialization\Json;
use Telegram\Bot\Actions;

class NotifyCommand extends TelegramCommandBase{

  /**
   * @var string Command Name
   */
  protected $name = "notify";

  /**
   * @var string Command Description
   */
  protected $description = "Присылать уведомления по заявке: /notify НОМЕР_ЗАЯВКИ КОД";

  /**
   * @inheritdoc
   */
  public function handle($arguments) {

    $this->logger->debug(Json::encode($arguments));
    // This will update the chat status to typing...
    $this->replyWithChatAction(['action' => Actions::TYPING]);
    // todo: реализовать подписку на уведомления по заявке

    $update = $this->getUpdate();
    $id = $update->getMessage()->getMessageId();
    $fromid = $update->getMessage()->getFrom()->getId();

    $this->replyWithMessage(['text' => 'Hello! /notify command']);
  }
}