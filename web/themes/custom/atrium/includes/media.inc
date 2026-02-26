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
  $site_config = \Drupal::config('system.site');

  $variables['html_attributes'] = new Attribute();
  // <html> element attributes.
  $language_interface = \Drupal::languageManager()->getCurrentLanguage();
  $variables['html_attributes']['lang'] = $language_interface->getId();

  // Head and page titles.
  $title = t($variables['resource']->getTitle());
  $variables['head_title'] = [
    'title' => $title,
    'name' => $site_config->get('name'),
  ];
  $variables['title'] = $title;
}
