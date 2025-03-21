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
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    
    $entity = $this->entity;
    
    $form['name']['widget'][0]['value']['#description'] = $this->t('Enter a name for this card design.');
    
    // Hide the data field and provide a link to the designer interface
    $form['data']['#access'] = FALSE;
    
    if (!$entity->isNew()) {
      $form['designer_link'] = [
        '#type' => 'markup',
        '#markup' => '<div class="form-item">' . $this->t('Edit this design in the <a href="@url">card designer</a>.', [
          '@url' => '/bcard-designer/' . $entity->id(),
        ]) . '</div>',
      ];
    }
    
    return $form;
  }

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