<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 19.03.2017
 * Time: 23:09
 */

namespace Drupal\telegram_bots_api;


interface TelegramBotInterface {

  /**
   * Provide a bot name.
   *
   * @return string
   */
  public function bot();

  /**
   * Provide a description of the sandwich.
   *
   * @return string
   *   A string description of the sandwich.
   */
  public function description();

  /**
   * Provide the token.
   *
   * @return string
   *   Token fo this bot.
   */
  public function token();

  /**
   * Webhook mehtod
   *
   *
   * @param array $update
   *   Update object.
   *
   * @return string
   *   Description of the sandwich that was just ordered. //todo : fix
   */
  public function webhook($update);

}