<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 10.03.2017
 * Time: 9:53
 */

namespace Drupal\telegram_holiday\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

abstract class QuestionCommand extends Command implements QuestionInterface {

  /**
   * @var array
   */
  public $questions;


  function __construct() {
    $this->questions = $this->defaultQuestions();
  }

  public function getQuestions() {
    return $this->questions;
  }

  public function getQuestion($question) {
    return $this->questions[$question];
  }

  /**
   * @return array
   */
  abstract public function defaultQuestions();
}