<?php
/**
 * Created by PhpStorm.
 * User: Lex
 * Date: 10.03.2017
 * Time: 17:58
 */

namespace Drupal\telegram_holiday\Commands;


interface QuestionInterface {

  /**
   * @return array
   */
  public function defaultQuestions();
}