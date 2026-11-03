<?php

namespace Drupal\xlsx\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Database\Database;

/**
 * Import zip files (forms images).
 *
 * @ingroup xlsx
 */
class FormsImagesUploadForm extends ConfigFormBase {

  protected $path = 'public://xlsx/forms';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['xlsx.forms_images_form'];
  }

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'forms_images_form_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $xlsx = NULL) {
    $form['zip_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Forms images'),
      '#upload_location' => $this->path,
      '#upload_validators' => [
        'file_validate_extensions' => ['zip'],
      ],
      '#description' => $this->t('Upload forms thumbnails zip file.'),
    ];
    $form['remove'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove previous imports'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $zip_file = $form_state->getValue('zip_file');
    if (!empty($zip_file[0])) {
      $file = File::load($zip_file[0]);
      $file->save();

      $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager');
      $file_path = $stream_wrapper_manager->getViaUri($file->getFileUri())->realpath();

      if (!empty($form_state->getValue('remove'))) {
        $storage_handler = \Drupal::entityTypeManager()->getStorage('jcc_form_thumbnail');
        $entities = $storage_handler->loadByProperties(['status' => 1]);
        $storage_handler->delete($entities);
      }

      $batch = [
        'title' => $this->t('Unzipping thumbnails'),
        'finished' => '\Drupal\xlsx\XlsxBatchOps::completeImportCallback',
        'operations' => [],
      ];

      $zip = new \ZipArchive();
      if ($zip->open($file_path) === TRUE) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
          $filename = $zip->getNameIndex($i);
          $contents = $zip->getFromIndex($i);
          if ($filename !== FALSE && $contents !== FALSE) {
            $batch['operations'][] = ['\Drupal\xlsx\XlsxBatchOps::uploadThumbnail', [$filename, $this->path, $contents]];
          }
        }
        $zip->close();
      }

      batch_set($batch);

      \Drupal::messenger()->addStatus($this->t('Successfully uploaded and processed.'));
    }
  }

}
