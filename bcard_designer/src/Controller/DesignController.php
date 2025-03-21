<?php

namespace Drupal\bcard_designer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the card designer editor.
 */
class DesignController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * Renders the business card designer editor.
   */
  public function editor() {
    return [
      '#theme' => 'card_designer',
      '#attached' => [
        'library' => ['bcard_designer/designer'],
      ],
    ];
  }

}