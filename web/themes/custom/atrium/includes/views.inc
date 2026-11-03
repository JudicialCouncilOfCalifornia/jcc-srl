<?php

use Drupal\node\Entity\Node;

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function atrium_theme_suggestions_views_view_alter(array &$suggestions, array $variables) {
  // Add a suggestion based on the view name and the current display.
  $view = $variables['view'];
  $name = $view->id();
  $display = $view->current_display;
  $suggestions[] = 'views_view__' . $name;
  $suggestions[] = 'views_view__' . $name . '__' . $display;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function atrium_preprocess_views_view(&$variables) {
  if ($variables['id'] == 'find_courts') {
    // See srl_court_finder module.
    $variables['alert_enabled'] = \Drupal::state()->get('court_finder__alert_enabled');
    $variables['alert_title'] = \Drupal::state()->get('court_finder__alert_title');
    $variables['alert_text'] = \Drupal::state()->get('court_finder__alert_text');
    $variables['alert_link_text'] = \Drupal::state()->get('court_finder__alert_link_text');
  } elseif($variables['id'] == 'solr_search') {
    $vid = 'search_categories';
    $search_arg = trim($variables['view']->exposed_data['search_api_fulltext']);
    //Checking if bew argument exists as a taxonomy term name
    $term_lookup = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $search_arg, 'vid' => $vid]);
    if (!empty($term_lookup)){
      $variables['featured_view'] = array_key_first($term_lookup);
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function atrium_preprocess_views_view__switchboard(&$variables) {
  if (!empty($variables['rows'][0]['#view']->result[0]->_relationship_entities['field_parent'])) {
    $parent = $variables['rows'][0]['#view']->result[0]->_relationship_entities['field_parent'];
    $children_primary = $parent->field_children_primary;
    $children_secondary = $parent->field_children_secondary;

    $variables['parent_howto'] = $parent;
    if ($children_primary) {
      $variables['children_primary'] = __jcc_suppressArchived($children_primary);
    }
    if ($children_secondary) {
      $variables['children_secondary'] = __jcc_suppressArchived($children_secondary);
    }
  }
}



/**
 * Suppresses archived content.
 */
function __jcc_suppressArchived($children) {
  $visible_content = [];
  foreach ($children as $item) {
    $node = $item->entity->field_page->entity;

    if ($node && $node->get('moderation_state')->getString() != 'archived') {
      array_push($visible_content, $item->entity);
    }
  }

  return $visible_content;
}
