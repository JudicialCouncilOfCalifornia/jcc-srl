<?php

/**
 * @file
 * Theme functions for the media template.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_HOOK().
 */
function atrium_preprocess_media_oembed_iframe(&$variables) {
  $variables['html_attributes'] = new Attribute();
  // <html> element attributes.
  $language_interface = \Drupal::languageManager()->getCurrentLanguage();
  $variables['html_attributes']['lang'] = $language_interface->getId();
}
