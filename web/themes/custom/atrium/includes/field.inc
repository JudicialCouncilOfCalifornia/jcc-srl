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
 *
 * @param string $field
 *   The field being audited.
 * @param string $text
 *   The field content to audit.
 *
 * @return string
 *   The audited field content.
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
  $text = preg_replace($pattern, '<span aria-hidden="true">$0</span>', $text);

  // ADA support for alert banner type context (ADA-963).
  // Adds screen reader only text for alert type if missing.
  if (strpos($text, 'jcc-alert') !== FALSE) {
    // 1. DOMDocument setup for parsing and modifying HTML.
    $dom = new \DOMDocument();
    $previous = libxml_use_internal_errors(TRUE);
    $dom->loadHTML('<meta charset="UTF-8"><div>' . $text . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    // 2. Ensure HTML is ready for DOM manipulation.
    $xpath = new \DOMXPath($dom);
    $has_class = function ($name) {
      return 'contains(concat(" ", normalize-space(@class), " "), " ' . $name . ' ")';
    };
    // 3. Add alert type screen reader context if missing.
    foreach ($xpath->query('//*[' . $has_class('jcc-alert') . ']') as $alert) {
      if ($xpath->query('.//*[' . $has_class('usa-sr-only') . ']', $alert)->length === 0) {
        $heading = $xpath->query('.//h4[' . $has_class('jcc-alert__heading') . ']', $alert)->item(0);
        // Without a heading, the label goes at the start of the alert body.
        $target = $heading ?? $xpath->query('.//*[' . $has_class('jcc-alert__body') . ']', $alert)->item(0);

        if ($target) {
          $label = 'Alert:';
          if (preg_match('/\busa-alert--(warning|error|success|info)\b/', $alert->getAttribute('class'), $matches)) {
            $label = ucfirst($matches[1]) . ' alert:';
          }
          $tag = $heading ? 'span' : 'p';
          $element = $dom->createElement($tag, $label);
          $element->setAttribute('class', 'usa-sr-only');
          $target->insertBefore($element, $target->firstChild);
        }
      }
    }
    // 4. Replace original HTML with modified content from DOMDocument.
    $wrapper = $dom->getElementsByTagName('div')->item(0);
    $output = '';
    foreach ($wrapper->childNodes as $node) {
      $output .= $dom->saveHTML($node);
    }
    $text = $output;
  }

  return $text;
}
