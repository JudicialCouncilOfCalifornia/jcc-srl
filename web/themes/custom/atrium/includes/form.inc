<?php

/**
 * @file
 * Theme and preprocess functions for forms.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_form_element().
 */
function atrium_preprocess_form_element(array &$variables) {
  if (!isset($variables['label'])) {
    return;
  }

  if (isset($variables['element']['#form_id'])) {
    $variables['label']['#form_id'] = $variables['element']['#form_id'];
  }
  if (isset($variables['element']['#type'])) {
    $variables['label']['#form_element_type'] = $variables['element']['#type'];
  }
  if (isset($variables['element']['#attributes']['data-twig-suggestion']['webform'])) {
    $variables['label']['#attributes']['webform'] = $variables['element']['#attributes']['data-twig-suggestion']['webform'];
  }
}

/**
 * Implements hook_form_alter().
 */
function atrium_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  atrium_attach_form_id($form, $form_id);

  if (isset($form['#webform_id'])) {
    $form['#webform_id_trunc'] = $form['#webform_id'];

    //Patternlab javascript needs to be reloaded on ajax calls for webform
    $request = \Drupal::request();

    $is_ajax = $request->isXmlHttpRequest();
    if(isset($form['is_ajax']) && !empty($form['is_ajax']) && $is_ajax) {
      $form['is_ajax'] = true;
    }
  }
  if (isset($form['#attributes']['class']) && in_array('wayfinder', $form['#attributes']['class']))  {
    atrium_add_suggestion($form, 'webform', 'wayfinder');
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function atrium_theme_suggestions_form_alter(array &$suggestions, array $variables) {
  if (isset($variables['element']['#form_id'])) {
    $suggestions[] = 'form__' . $variables['element']['#form_id'];
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function atrium_theme_suggestions_form_element_alter(array &$suggestions, array $variables) {
  $element = $variables['element'];

  $suggestions[] = 'form_element__type__' . $element['#type'];
  if (isset($element['#form_id'])) {
    $suggestions[] = 'form_element__form_id__' . $element['#form_id'];
    $suggestions[] = 'form_element__' . $element['#form_id'] . '__' . $element['#type'];
  }
  if (isset($element['#attributes']['data-twig-suggestion']['webform'])) {
    $webform_suggestion = str_replace(['-'], '_', $element['#attributes']['data-twig-suggestion']['webform']);
    $suggestions[] = 'form_element__' . $webform_suggestion;
    $suggestions[] = 'form_element__type__' . $element['#type'] . '__' . $webform_suggestion;
  }
  if (isset($variables['element']['#webform'])) {
    $suggestions[] = 'form_element__' . $variables['element']['#webform'] . '__' . $variables['element']['#type'];
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function atrium_theme_suggestions_form_element_label_alter(array &$suggestions, array $variables) {
  $element = $variables['element'];

  if (isset($element['#form_element_type'])) {
    $suggestions[] = 'form_element_label__type__' . $element['#form_element_type'];
  }
  if (isset($element['#form_id'])) {
    $suggestions[] = 'form_element_label__form_id__' . $element['#form_id'];
  }
  if (isset($element['#form_element_type']) && isset($element['#form_id'])) {
    $suggestions[] = 'form_element_label__' . $element['#form_id'] . '__' . $element['#form_element_type'];
  }
  if (isset($element['#attributes']['webform'])) {
    $webform_suggestion = str_replace(['-'], '_', $element['#attributes']['webform']);
    $suggestions[] = 'form_element_label__' . $webform_suggestion;
  }
  if (isset($webform_suggestion) && isset($element['#form_element_type'])) {
    $suggestions[] = 'form_element_label__type__' . $element['#form_element_type'] . '__' . $webform_suggestion;
  }
}

/**
 * Attaches form id to all form elements.
 *
 * @param $form
 *   The form or form element which children should have form id attached.
 * @param $form_id
 *   The form id attached to form elements.
 *
 * @return array
 */
function atrium_attach_form_id(&$form, $form_id) {
  foreach (Element::children($form) as $child) {
    if (!isset($form[$child]['#form_id'])) {
      $form[$child]['#form_id'] = $form_id;
    }
    atrium_attach_form_id($form[$child], $form_id);
  }
}

/**
 * Adds suggestions to form elements.
 */
function atrium_add_suggestion(&$form, $key, $suggestion) {
  foreach (Element::children($form) as $child) {
    $form[$child]['#attributes']['data-twig-suggestion'][$key] = $suggestion;
    atrium_add_suggestion($form[$child], $key, $suggestion);
  }
}
