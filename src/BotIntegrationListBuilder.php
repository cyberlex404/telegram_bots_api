<?php

namespace Drupal\telegram_bots_api;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Bot integration entities.
 */
class BotIntegrationListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Bot integration');
    $header['id'] = $this->t('Machine name');
    $header['plugin'] = $this->t('Plugin');
    $header['enabled'] = $this->t('Enabled');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /**
     * @var $entity \Drupal\telegram_bots_api\Entity\BotIntegration
     */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['plugin'] = $entity->plugin();
    $row['enabled'] = $entity->status()? $this->t('Enabled'): $this->t('Disabled');

    return $row + parent::buildRow($entity);
  }

}
