<?php

namespace Drupal\bcard_designer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides business logic for the Business Card Designer module.
 */
class BCardDesignerManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new BCardDesignerManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    FileSystemInterface $file_system,
    ConfigFactoryInterface $config_factory
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
    $this->configFactory = $config_factory;
  }

  /**
   * Saves a business card design.
   *
   * @param string $name
   *   The name of the design.
   * @param string $data
   *   The JSON data of the design.
   *
   * @return int|bool
   *   The ID of the saved design, or FALSE on failure.
   */
  public function saveDesign($name, $data) {
    $connection = \Drupal::database();
    $id = $connection->insert('bcard_designs')
      ->fields([
        'name' => $name,
        'data' => $data,
        'created' => time(),
        'changed' => time(),
      ])
      ->execute();
    return $id;
  }

  /**
   * Loads a business card design.
   *
   * @param int $id
   *   The ID of the design to load.
   *
   * @return array|bool
   *   An array containing the design data, or FALSE if not found.
   */
  public function loadDesign($id) {
    $connection = \Drupal::database();
    $design = $connection->select('bcard_designs', 'b')
      ->fields('b')
      ->condition('id', $id)
      ->execute()
      ->fetchAssoc();
    return $design;
  }

/**
 * Retrieves all card designs.
 *
 * @return \Drupal\Core\Entity\EntityInterface[]
 *   An array of card design entities.
 */
public function getAllDesigns() {
  $storage = $this->entityTypeManager->getStorage('card_design');
  return $storage->loadMultiple();
}
  // Add more methods as needed for managing designs and assets.
}