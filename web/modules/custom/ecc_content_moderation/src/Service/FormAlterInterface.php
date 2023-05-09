<?php

namespace Drupal\ecc_content_moderation\Service;


use Drupal\Core\Form\FormStateInterface;

/**
 * Class FormAlter.
 *
 * Form alter service for the Essex Content Moderation module.
 *
 * @package Drupal\ecc_content_moderation\Service
 */
interface FormAlterInterface {
  /**
   * Hide entity moderation form.
   *
   * @param array $form
   *   The form.
   * @param FormStateInterface $form_alter
   *   The form_state.
   *
   * @return void
   */
  public function hideEntityModerationForm(array &$form, FormStateInterface &$form_alter);
}
