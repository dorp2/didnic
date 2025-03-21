<?php

namespace Drupal\bcard_designer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\bcard_designer\BCardDesignerManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the business card designer.
 */
class CardDesignController extends ControllerBase {

  /**
   * The BCard Designer manager.
   *
   * @var \Drupal\bcard_designer\BCardDesignerManager
   */
  protected $bCardManager;

  /**
   * Constructs a new CardDesignController object.
   *
   * @param \Drupal\bcard_designer\BCardDesignerManager $bcard_manager
   *   The BCard Designer manager.
   */
  public function __construct(BCardDesignerManager $bcard_manager) {
    $this->bCardManager = $bcard_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bcard_designer.manager')
    );
  }

  /**
   * Displays the business card designer.
   *
   * @param int|null $id
   *   The ID of the design to edit, or NULL for a new design.
   *
   * @return array
   *   A render array for the designer page.
   */
  public function designer($id = NULL) {
    $design = NULL;
    if ($id) {
      $design = $this->bCardManager->loadDesign($id);
      if (!$design) {
        $this->messenger()->addError($this->t('Design not found.'));
        return $this->redirect('bcard_designer.admin_list');
      }
    }

    // Get fonts and backgrounds from configuration
    $config = $this->config('bcard_designer.settings');
    $fonts = $config->get('fonts') ?: [];
    $backgrounds = $config->get('backgrounds') ?: [];

    return [
      '#theme' => 'card_designer',
      '#design' => $design ? [
        'id' => $design->id(),
        'name' => $design->label(),
        'data' => $design->get('data')->value,
      ] : NULL,
      '#fonts' => $fonts,
      '#backgrounds' => $backgrounds,
      '#attached' => [
        'library' => [
          'bcard_designer/designer',
        ],
        'drupalSettings' => [
          'bcard_designer' => [
            'designId' => $id,
          ],
        ],
      ],
    ];
  }

  /**
   * Displays the admin list of business card designs.
   *
   * @return array
   *   A render array for the admin list page.
   */
  public function adminList() {
    $designs = $this->bCardManager->getAllDesigns();

    $design_data = [];
    foreach ($designs as $design) {
      $design_data[] = [
        'id' => $design->id(),
        'name' => $design->label(),
        'path' => '/bcard-designer/preview/' . $design->id(),
      ];
    }

    return [
      '#theme' => 'admin_card_designs',
      '#designs' => $design_data,
    ];
  }

  /**
   * Displays the list of uploaded assets.
   *
   * @return array
   *   A render array for the assets list page.
   */
  public function listAssets() {
    $config = $this->config('bcard_designer.settings');
    $fonts = $config->get('fonts') ?: [];
    $backgrounds = $config->get('backgrounds') ?: [];

    return [
      '#theme' => 'admin_card_assets',
      '#fonts' => $fonts,
      '#backgrounds' => $backgrounds,
    ];
  }

  /**
   * Saves a design via AJAX.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function saveDesignAjax(Request $request) {
    $content = json_decode($request->getContent(), TRUE);

    if (empty($content['name']) || empty($content['data'])) {
      return new JsonResponse(['success' => FALSE, 'message' => $this->t('Missing required data.')], 400);
    }

    $id = !empty($content['id']) ? $content['id'] : NULL;

    if ($id) {
      $design = $this->bCardManager->loadDesign($id);
      if ($design) {
        $design->set('name', $content['name']);
        $design->set('data', $content['data']);
        $design->save();
        return new JsonResponse(['success' => TRUE, 'id' => $design->id()]);
      }
      return new JsonResponse(['success' => FALSE, 'message' => $this->t('Design not found.')], 404);
    }
    else {
      $id = $this->bCardManager->saveDesign($content['name'], $content['data']);
      if ($id) {
        return new JsonResponse(['success' => TRUE, 'id' => $id]);
      }
      return new JsonResponse(['success' => FALSE, 'message' => $this->t('Failed to save design.')], 500);
    }
  }
  /**
   * Deletes an asset.
   *
   * @param string $type
   *   The type of asset ('font' or 'background').
   * @param int $id
   *   The index of the asset in the configuration.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function deleteAsset($type, $id) {
    $config = $this->config('bcard_designer.settings');

    if ($type === 'font') {
      $fonts = $config->get('fonts') ?: [];
      if (isset($fonts[$id])) {
        $font = $fonts[$id];
        if (isset($font['fid'])) {
          $file = File::load($font['fid']);
          if ($file) {
            $file->delete();
          }
        }
        unset($fonts[$id]);
        $this->configFactory()->getEditable('bcard_designer.settings')
          ->set('fonts', array_values($fonts))
          ->save();
        $this->messenger()->addStatus($this->t('Font deleted successfully.'));
      }
    }
    elseif ($type === 'background') {
      $backgrounds = $config->get('backgrounds') ?: [];
      if (isset($backgrounds[$id])) {
        $bg = $backgrounds[$id];
        if (isset($bg['fid'])) {
          $file = File::load($bg['fid']);
          if ($file) {
            $file->delete();
          }
        }
        unset($backgrounds[$id]);
        $this->configFactory()->getEditable('bcard_designer.settings')
          ->set('backgrounds', array_values($backgrounds))
          ->save();
        $this->messenger()->addStatus($this->t('Background deleted successfully.'));
      }
    }

    return $this->redirect('bcard_designer.list_assets');
  }
}