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
   * @param string $permission
   *   Permission required to have access to form.
   *
   * @return void
   */
  public function accessPermission(array &$form, string $permission);
}
