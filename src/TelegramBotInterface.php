<?php

namespace Drupal\telegram_bots_api;


use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
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
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function webHook(Request $request);

  /**
   * @param \Telegram\Bot\Objects\Update $update
   * @return void
   */
  public function executeUpdate(Update $update);

  /**
   * @return \Telegram\Bot\Api
   * @throws \Exception
   */
  public function api();

  public function getBotInfo();

  public function setWebHook(Url $url);


  public function webhookInfo();



}