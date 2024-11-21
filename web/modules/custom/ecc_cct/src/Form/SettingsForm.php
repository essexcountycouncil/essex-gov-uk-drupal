<?php

declare(strict_types=1);

namespace Drupal\ecc_cct\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure ECC Cookie Consent Tracker settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ecc_cct_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ecc_cct.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['enable'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enable tracking'),
      '#options' => [
        0 => $this->t('Disabled'),
        1 => $this->t('Enabled'),
      ],
      '#config_target' => 'ecc_cct.settings:enable',
    ];
    return parent::buildForm($form, $form_state);
  }

}
