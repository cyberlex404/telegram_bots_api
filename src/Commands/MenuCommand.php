<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 09.03.2017
 * Time: 22:37
 */

namespace Drupal\telegram_holiday\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class MenuCommand extends Command{

  /**
   * @var string Command Name
   */
  protected $name = "menu";

  /**
   * @var string Command Description
   */
  protected $description = "Menu command";

  /**
   * @inheritdoc
   */
  public function handle($arguments)
  {



    // This will update the chat status to typing...
    $this->replyWithChatAction(['action' => Actions::TYPING]);
    $keyboard = [
      ['7', '8', '9'],
      ['4', '5', '6'],
      ['1', '2', '3'],
      ['0']
    ];
    $reply_markup = $this->telegram->replyKeyboardMarkup([
      'keyboard' => $keyboard,
      'resize_keyboard' => true,
      'one_time_keyboard' => true
    ]);
    $this->replyWithMessage([
      'text' => 'Menu ' . $this->getArguments(),
      'reply_markup' => $reply_markup
    ]);

  }
}