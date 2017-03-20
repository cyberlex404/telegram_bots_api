<?php

namespace Drupal\telegram_bots_api;


use Telegram\Bot\Objects\Update;

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
   * @return \Telegram\Bot\Commands\Command[]
   */
  public function commands();

  /**
   * Webhook mehtod
   *
   * @return void
   */
  public function webhook();

  /**
   * @param \Telegram\Bot\Objects\Update $update
   * @return void
   */
  public function executeUpdate(Update $update);

  /**
   * @return \Telegram\Bot\Api
   */
  public function api();



}