<?php

namespace Drupal\bcard_designer\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for creating and editing card designs.
 */
class CardDesignForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    $message = $status == SAVED_NEW
      ? $this->t('Created the %label card design.', ['%label' => $entity->label()])
      : $this->t('Updated the %label card design.', ['%label' => $entity->label()]);
    $this->messenger()->addStatus($message);

    $form_state->setRedirect('bcard_designer.admin_list');
  }

}