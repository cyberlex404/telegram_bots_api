<?php

namespace Drupal\telegram_bots_api\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class HelpCommand extends Command{

  /**
   * @var string Command Name
   */
  protected $name = "help";

  /**
   * @var string Command Description
   */
  protected $description = "Start bot";

  /**
   * @inheritdoc
   */
  public function handle($arguments)
  {
    // This will send a message using `sendMessage` method behind the scenes to
    // the user/chat id who triggered this command.
    // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
    // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
    $this->replyWithChatAction(['action' => Actions::TYPING]);
    $this->replyWithMessage(['text' => 'Hello! Welcome to Holiday bot, Here are our available commands:']);

    // This will update the chat status to typing...



   // $this->triggerCommand('subscribe');
  }
}