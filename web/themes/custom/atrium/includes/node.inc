<?php

/**
 * @file
 * Theme functions for the node template.
 */

use Drupal\Core\Url;

/**
 * Implements hook_preprocess_HOOK().
 */
function atrium_preprocess_node(&$variables) {
  $node = $variables['node'];
  switch ($node->getType()) {
    case 'court':
      break;
    case 'timeline':
      break;
    default:
      // Previous page link label if in use.
      $variables['previous_label'] = t('Previous Page');
      // Index page link if tagged as case type.
      if ($node->field_case_type_override && !$node->get('field_case_type_override')->isEmpty()) {
        $case_type_override = $node->get('field_case_type_override');
        $variables['case_index']['url'] = Url::fromUri($case_type_override[0]->get('uri')->getValue());
        $variables['case_index']['label'] = t('Index: All Pages');
      } else {
        $terms = $node->get('field_case_types')->referencedEntities();
        if ($terms) {
          if (!$terms[0]->get('field_url')->isEmpty()) {
            $lang_code = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $term_custom_uri = $terms[0]->get('field_url')->getValue();
            $term_custom_uri_translated = \Drupal::service('entity.repository')->getTranslationFromContext($terms[0])->get('field_url')->getValue();
            // If different language, use translated index else default English
            if ($lang_code != 'en' && $term_custom_uri_translated) {
              $term_custom_uri = $term_custom_uri_translated;
            }
            $variables['case_index']['url'] = Url::fromUri($term_custom_uri[0]['uri']);
            $variables['case_index']['label'] = t('Index: All Pages');
          } else {
            break;
          }
        }
      }

  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function atrium_preprocess_node__srl_onramp__full(&$variables) {
  $node = $variables['node'];
  $paragraphs = $node->get('field_paragraphs');
  if ($paragraphs) {
    $has_create_anchor = FALSE;
    foreach ($paragraphs as $item) {
      $paragraph = $item->entity;
      if ($paragraph->getType() === 'postcard') {
        if ($paragraph->get('field_create_anchor')->value == 1) {
          $has_create_anchor = TRUE;
          break;
        }
      }
    }
    $variables['has_jump_nav'] = $has_create_anchor;
  }
}
