<?php

namespace Drupal\bcard_designer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Business Card Designer settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bcard_designer_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bcard_designer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bcard_designer.settings');

    $form['design_directory'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Design Directory'),
      '#default_value' => $config->get('design_directory') ?: 'public://designs',
      '#description' => $this->t('The directory where designs are stored.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('bcard_designer.settings')
      ->set('design_directory', $form_state->getValue('design_directory'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}