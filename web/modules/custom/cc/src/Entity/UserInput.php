<?php

namespace Drupal\cc\Entity;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the User input entity.
 *
 * @ingroup cc
 *
 * @ContentEntityType(
 *   id = "cc_user_input",
 *   label = @Translation("User input"),
 *   handlers = {
 *     "storage" = "Drupal\cc\UserInputStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cc\UserInputListBuilder",
 *     "views_data" = "Drupal\cc\Entity\UserInputViewsData",
 *     "translation" = "Drupal\cc\UserInputTranslationHandler",
 *     "access" = "Drupal\cc\UserInputAccessControlHandler",
 *     "inline_form" = "Drupal\cc\UserInputInlineEntityFormController",
 *   },
 *   base_table = "cc_user_input",
 *   data_table = "cc_user_input_field_data",
 *   revision_table = "cc_user_input_revision",
 *   revision_data_table = "cc_user_input_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer user input entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 * )
 */
class UserInput extends EditorialContentEntityBase implements UserInputInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    if (isset($fields['revision_log_message'])) {
      $fields['revision_log_message']->setDisplayOptions('form', [
        'type' => 'hidden',
      ]);
    }

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the User input.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the User input is published.'))
      ->setDisplayOptions('form', [
        'type' => 'hidden',
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['selection_type'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Selection type'))
      ->setDescription(t('User Input selection type.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDefaultValue(UserInputInterface::SELECTION_TYPE_SELECT)
      ->setSetting('allowed_values', [
        UserInputInterface::SELECTION_TYPE_SELECT => t('Select'),
        UserInputInterface::SELECTION_TYPE_RADIO => t('Radio'),
        UserInputInterface::SELECTION_TYPE_CHECKBOX => t('Checkbox'),
      ])
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -3,
      ]);

    $fields['required'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Required'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayOptions('form', [
        'type' => 'checkbox',
        'weight' => -2,
      ]);

    $fields['input_items'] = BaseFieldDefinition::create('cc_user_input_item')
      ->setLabel(t('Items'))
      ->setDescription(t('User Input items.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => -1,
        'settings' => [
          'rows' => 2,
        ],
      ]);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSelectionType() {
    return $this->get('selection_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSelectionType($type) {
    $this->set('selection_type', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isRequired() {
    return $this->get('required')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRequired($required) {
    $this->set('required', $required);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputItems() {
    return $this->get('input_items')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function setInputItems(array $input_items) {
    $this->set('input_items', $input_items);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputItemsOptions() {
    $options = [];
    foreach ($this->getInputItems() as $item) {
      $options['item_' . $item['id']] = Html::escape($item['value']);
    }
    return $options;
  }

}
