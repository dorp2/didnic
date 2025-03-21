<?php

namespace Drupal\bcard_designer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for uploading assets (fonts and backgrounds).
 */
class AssetUploadForm extends FormBase {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Constructs a new AssetUploadForm object.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   */
  public function __construct(FileSystemInterface $file_system) {
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bcard_designer_asset_upload_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['font'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Upload Font'),
    ];

    $form['font']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Name'),
      '#required' => TRUE,
    ];

    $form['font']['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Font File'),
      '#description' => $this->t('Upload a font file (TTF, OTF, WOFF, WOFF2).'),
      '#upload_location' => 'public://bcard_designer/fonts/',
      '#upload_validators' => [
        'file_validate_extensions' => ['ttf otf woff woff2'],
      ],
      '#required' => TRUE,
    ];

    $form['background'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Upload Background'),
    ];

    $form['background']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Background Name'),
      '#required' => FALSE,
    ];

    $form['background']['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Background Image'),
      '#description' => $this->t('Upload a background image (JPG, PNG, SVG).'),
      '#upload_location' => 'public://bcard_designer/backgrounds/',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png svg'],
        'file_validate_image_resolution' => ['3000x3000', '100x100'],
      ],
      '#required' => FALSE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
    ];

    return $form;
  }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      $config = $this->config('bcard_designer.settings');
      $fonts = $config->get('fonts') ?: [];
      $backgrounds = $config->get('backgrounds') ?: [];

      // Process font upload
      if ($font_file = $form_state->getValue(['font', 'file'])) {
        $file = File::load($font_file[0]);
        if ($file) {
          $file->setPermanent();
          $file->save();

          $font_name = $form_state->getValue(['font', 'name']);
          $fonts[] = [
            'name' => $font_name,
            'url' => $file->createFileUrl(),
            'fid' => $file->id(),
          ];

          $this->messenger()->addStatus($this->t('Font @name uploaded successfully.', ['@name' => $font_name]));
        }
      }

      // Process background upload
      if ($bg_file = $form_state->getValue(['background', 'file'])) {
        $file = File::load($bg_file[0]);
        if ($file) {
          $file->setPermanent();
          $file->save();

          $bg_name = $form_state->getValue(['background', 'name']) ?: $file->getFilename();
          $backgrounds[] = [
            'name' => $bg_name,
            'url' => $file->createFileUrl(),
            'fid' => $file->id(),
          ];

          $this->messenger()->addStatus($this->t('Background @name uploaded successfully.', ['@name' => $bg_name]));
        }
      }

      // Save configuration
      $this->configFactory()
        ->getEditable('bcard_designer.settings')
        ->set('fonts', $fonts)
        ->set('backgrounds', $backgrounds)
        ->save();

      $form_state->setRedirect('bcard_designer.list_assets');
    }

  }