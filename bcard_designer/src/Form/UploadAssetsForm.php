<?php

namespace Drupal\bcard_designer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class UploadAssetsForm extends FormBase {

  public function getFormId() {
    return 'bcard_designer_upload_assets_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['font'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Font'),
      '#upload_location' => 'public://bcard_designer/fonts/',
      '#upload_validators' => [
        'file_validate_extensions' => ['ttf otf woff woff2'],
      ],
      '#multiple' => TRUE,
    ];

    $form['background'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Background'),
      '#upload_location' => 'public://bcard_designer/backgrounds/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
      ],
      '#multiple' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fonts = $form_state->getValue('font');
    $backgrounds = $form_state->getValue('background');

    foreach ($fonts as $fid) {
      $file = File::load($fid);
      $file->setPermanent();
      $file->save();
    }

    foreach ($backgrounds as $fid) {
      $file = File::load($fid);
      $file->setPermanent();
      $file->save();
    }

    $this->messenger()->addMessage($this->t('Assets uploaded successfully.'));
  }
}