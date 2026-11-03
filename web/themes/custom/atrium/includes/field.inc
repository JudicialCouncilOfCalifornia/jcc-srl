<?php

/**
 * @file
 * Theme functions for the field template.
 */

/**
 * Implements hook_node_field__node__body().
 */
function atrium_preprocess_field(&$variables) {
  $field_name = $variables['element']['#field_name'];

  if ($field_name) {
    switch ($field_name) {
      case 'body':
      case 'field_heading':
      case 'field_column_first':
      case 'field_column_second':
      case 'field_column_content_right':
      case 'field_columns':
      case 'field_body':
      case 'field_description':
      case 'field_details':
      case 'field_complete':
      case 'field_additional':
      case 'field_top_read_more_modal':
      case 'field_bottom_read_more_modal':
        if (isset($variables['items'])) {
          foreach ($variables['items'] as &$element) {
            if (isset($element['content']['#text'])) {
              // Bulk modify content concerns dynamically on render.
              $element['content']['#text'] = __jcc_content_auditing($element['content']['#text']);
            }
          }
        }
        break;
    }
  }
}

/**
 * Helper function for extended content auditing.
 */
function __jcc_content_auditing($text) {
  // ADA support for Unicode symbols (ADA-368).
  // Requires <span aria-hidden> to be allowed text format.
  // TO-DO: Revisit if still a need with Drupal 10/11.
  // 1. Suppress symbol aria-hidden attempts before ADA-368 fix.
  $manual_spans = '/<span>(.*?\p{So}.*?)<\/span>/um';
  $text = preg_replace($manual_spans, '$1', $text);
  // 2. Address symbols missing aria-hidden use.
  $pattern = '/<span[^>]*\baria-hidden\s*=\s*["\']?[^>]*>(.*?)<\/span>(*SKIP)(*FAIL)|\p{So}+/um';
  return preg_replace($pattern, '<span aria-hidden="true">$0</span>', $text);
}
