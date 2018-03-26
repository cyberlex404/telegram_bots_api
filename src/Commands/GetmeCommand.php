<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 13.03.2018
 * Time: 6:18
 */

namespace Drupal\telegram_bots_api\Commands;

use Telegram\Bot\Actions;

class GetmeCommand extends TelegramCommandBase{

  /**
   * @var string Command Name
   */
  protected $name = "meinfo";

  /**
   * @var string Command Description
   */
  protected $description = "Show /me information";

  public function handle($arguments) {
    $this->replyWithChatAction(['action' => Actions::TYPING]);

    $update = $this->getUpdate();
    $fromid = $update->getMessage()->getFrom()->getId();
    $meMessage = $fromid;
    //$meMessage = t("Your id $fromid");
    $this->replyWithMessage(['text' => $meMessage]);
  }


}