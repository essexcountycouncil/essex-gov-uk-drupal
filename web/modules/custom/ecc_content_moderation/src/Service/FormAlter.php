<?php

namespace Drupal\ecc_content_moderation\Service;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxy;

/**
 * {@inheritdoc}
 */
class FormAlter implements FormAlterInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    private AccountProxy $currentUser,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function hideEntityModerationForm(array &$form, FormStateInterface &$form_alter) {
    $form['#access'] = $this->getCurrentUser()->hasPermission('view entity moderation form');
  }

  /**
   * Get current_user service.
   *
   * @return AccountProxy
   *   The current_user service.
   */
  protected function getCurrentUser(): AccountProxy {
    return $this->currentUser;
  }
}
