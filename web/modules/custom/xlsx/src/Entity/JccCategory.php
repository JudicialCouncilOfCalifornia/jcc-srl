<?php

namespace Drupal\xlsx\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Defines the JccCategory entity.
 *
 * @ingroup xlsx
 *
 * @ContentEntityType(
 *   id = "jcc_category",
 *   label = @Translation("JCC Category"),
 *   base_table = "jcc_category",
 *   translatable = FALSE,
 *   admin_permission = "administer xlsx mapping",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 * )
 */
class JccCategory extends ContentEntityBase implements JccCategoryInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
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
  public function getCategoryLabel() {
    return $this->get('category_label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCategoryLabel($label) {
    $this->set('category_label', $label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfoUrl() {
    return $this->get('info_url')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setInfoUrl($info_url) {
    $this->set('info_url', $info_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfoLabel() {
    return $this->get('info_label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setInfoLabel($info_label) {
    $this->set('info_label', $info_label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPacketUrl() {
    return $this->get('packet_url')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPacketUrl($packet_url) {
    $this->set('packet_url', $packet_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPacketLabel() {
    return $this->get('packet_label')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPacketLabel($packet_label) {
    $this->set('packet_label', $packet_label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSynonym() {
    return $this->get('synonym')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSynonym($synonym) {
    $this->set('synonym', $synonym);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTermId() {
    if ($term = $this->get('term_id')->entity) {
      return $term->id();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setTermId($term_id) {
    $this->set('term_id', ['target_id' => $term_id]);
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
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $term_name = $this->getCategoryLabel();
    if ($terms = taxonomy_term_load_multiple_by_name($term_name, 'jcc_form_category')) {
      $term = reset($terms);
      $this->setTermId($term->id());
      if ($url = $this->getInfoUrl()) {
        $term->set('field_category_info_link', [
          'uri' => $url,
          'title' => $this->getInfoLabel(),
        ]);
      }
      if ($url = $this->getPacketUrl()) {
        $term->set('field_category_form_packets_link', [
          'uri' => $url,
          'title' => $this->getPacketLabel(),
        ]);
      }
      $term->set('field_synonyms', $this->getSynonym());
      $term->save();
    }
    else {
      $values = [
        'name' => $term_name,
        'field_synonyms' => $this->getSynonym(),
        'vid' => 'jcc_form_category',
      ];
      if ($url = $this->getInfoUrl()) {
        $values['field_category_info_link'] = [
          'uri' => $url,
          'title' => $this->getInfoLabel(),
        ];
      }
      if ($url = $this->getPacketUrl()) {
        $values['field_category_form_packets_link'] = [
          'uri' => $url,
          'title' => $this->getPacketLabel(),
        ];
      }
      $term = Term::create($values);
      $term->save();
      $this->setTermId($term->id());
    }
    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Disclaimer entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Category ID'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(TRUE);

    $fields['category_label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Category Label'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['info_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Info URL'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['info_label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Info Label'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['packet_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Packet URL'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['packet_label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Packet Label'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['synonym'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Synonym'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(FALSE);

    $fields['term_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Referenced Term'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term')
      ->setSetting('handler_settings', [
        'target_bundles' => [
           'jcc_form_category' => 'jcc_form_category'
        ]
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 3,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '10',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['status']->setDescription(t('Status.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -10,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
