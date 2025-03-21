<?php

namespace Drupal\bcard_designer\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Card Designer' Block.
 *
 * @Block(
 *   id = "card_designer_block",
 *   admin_label = @Translation("Card Designer Block"),
 *   category = @Translation("Business Card Designer")
 * )
 */
class CardDesignerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'card_designer',
      '#attached' => [
        'library' => [
          'bcard_designer/designer',
        ],
      ],
    ];

    return $build;
  }

}