<?php

namespace Drupal\bcard_designer\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the card design entity.
 *
 * @ContentEntityType(
 *   id = "card_design",
 *   label = @Translation("Card Design"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bcard_designer\CardDesignListBuilder",
 *     "form" = {
 *       "default" = "Drupal\bcard_designer\Form\CardDesignForm",
 *       "add" = "Drupal\bcard_designer\Form\CardDesignForm",
 *       "edit" = "Drupal\bcard_designer\Form\CardDesignForm",
 *       "delete" = "Drupal\bcard_designer\Form\CardDesignDeleteForm",
 *     },
 *     "access" = "Drupal\bcard_designer\CardDesignAccessControlHandler",
 *   },
 *   base_table = "bcard_designs",
 *   admin_permission = "administer card designs",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/card-design/{card_design}",
 *     "add-form" = "/admin/content/card-design/add",
 *     "edit-form" = "/admin/content/card-design/{card_design}/edit",
 *     "delete-form" = "/admin/content/card-design/{card_design}/delete",
 *     "collection" = "/admin/content/card-designs",
 *   }
 * )
 */
class CardDesign extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the card design.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['data'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Design Data'))
      ->setDescription(t('The JSON data of the card design.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the card design was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the card design was last edited.'));
    return $fields;
  }

}