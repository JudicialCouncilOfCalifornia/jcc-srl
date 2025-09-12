<?php

namespace Drupal\xlsx\Plugin\xlsx\cell;

use Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\xlsx\Plugin\XlsxCellBase;

/**
 * Download remote File and save as a Media.
 *
 * @XlsxCell(
 *   id = "save_as_media",
 *   name = @Translation("Save As Media"),
 *   description = @Translation("Download remote file, save it as media entity and attach to an entity."),
 *   field_types = {
 *     "entity_reference",
 *   }
 * )
 */
class SaveAsMedia extends XlsxCellBase {

  /**
   * {@inheritdoc}
   */
  public function import($entity, $field_name, $value, $mapped_fields, $data_array, $worksheet_index) {
    if ($entity->hasField($field_name)) {
      $filename = $value . '.jpg';
      $url = 'https://github.com/JudicialCouncilOfCalifornia/jcc-srl-courthouse-maps/raw/master/' . $filename;
      $directory = 'public://courthouse/map';
      $destination = $directory . '/' . $filename;
      $mapping = \Drupal::database()->select('xlsx_entity_mapping', 'm')
        ->fields('m')
        ->condition('m.entity_type', 'media:image')
        ->condition('m.hash', md5($value))
        ->execute()
        ->fetchObject();
      if (!empty($mapping->entity_id)) {
        return ['target_id' => $mapping->entity_id];
      }
      else {
        \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
        if ($file = system_retrieve_file($url, $destination, TRUE)) {
          $media = Media::create([
            'bundle' => 'image',
            'uid' => \Drupal::currentUser()->id(),
            'field_media_image' => [
              'target_id' => $file->id(),
            ],
          ]);
          $media->setName($value)->setPublished(TRUE)->save();
          \Drupal::database()->insert('xlsx_entity_mapping')
            ->fields([
              'entity_type' => 'media:image',
              'entity_id' => $media->id(),
              'hash' => md5($value),
            ])
            ->execute();
          return ['target_id' => $media->id()];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function export($entity, $field_name, $value) {
    if ($entity->hasField($field_name) && !$entity->get($field_name)->isEmpty()) {
      if ($ref = $entity->get($field_name)->first()->getValue()) {
        if ($media = Media::load($ref['target_id'])) {
          if ($media_field = $media->get('field_media_image')->first()->getValue()) {
            if ($file =  File::load($media_field['target_id'])) {
              return basename($file->getFileUri(), '.jpg');
            }
          }
        }
      }
    }
    return '';
  }

}
