<?php
/**
 * Implements template_preprocess_paragraph().
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An array of elements to display in view mode.
 *   - paragraph: The paragraph object.
 *   - view_mode: View mode; e.g., 'full', 'teaser'...
 */

use Drupal\Core\Url;
use Drupal\Core\Menu\MenuTreeParameters;

function atrium_preprocess_paragraph(&$variables) {
  /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
  $paragraph = $variables['paragraph'];

  if($paragraph->getType() == 'switchboard'){
    $parent = $paragraph->getParentEntity();
    if($parent->bundle() == 'overview'){

      $terms = $parent->get('field_case_types')->referencedEntities();
      $case_forms_uri = $parent->get('field_case_type_forms_page');
      $case_forms_url = $case_forms_uri->uri ? Url::fromUri($case_forms_uri->uri)->toString() : null;
      if(count($terms) == 1){
        if (!$terms[0]->get('field_url')->isEmpty()) {
          $variables['switchboard_footer']['label'] = t('Already know what you are looking for?');
          $term_custom_uri = $terms[0]->get('field_url')->getValue();

          if($case_forms_url){
            $variables['switchboard_footer']['buttons'][0]['url'] = $case_forms_url;
            $variables['switchboard_footer']['buttons'][0]['icon']['name'] = 'file';
          }
          $variables['switchboard_footer']['buttons'][1]['url'] = Url::fromUri($term_custom_uri[0]['uri']);
          $variables['switchboard_footer']['buttons'][1]['icon']['name'] = 'list';
          if($case_forms_url) {
            $variables['switchboard_footer']['buttons'][0]['title'] = t('See All Forms');
          }
          $variables['switchboard_footer']['buttons'][1]['title'] = t('Index: All Pages');

        }
      }
    }
  } elseif ($paragraph->getType() == 'hero_cards'){
    $variables['menu_links'] = [];
    $cards = $paragraph->get('field_cards');

    foreach ($cards->referencedEntities() as $card) {
      if($card->field_dropdown_menu[0]){
        $menu_fields = $card->field_dropdown_menu[0]->getValue();
        $menu_title = $menu_fields['target_id'];
        if(empty($variables['menu_links'][$menu_title])){
          $second_level = false;
          $menu_links = \Drupal::menuTree()->load($menu_title, new MenuTreeParameters());
          foreach ($menu_links as $key => $item) {
            $link_url = $item->link->getUrlObject();
            $link = $link_url->toString();
            $variables['menu_links'][$menu_title][$key] = ['title'=>$item->link->getTitle(),'url'=>$link];
            if($item->hasChildren){
              foreach ($item->subtree as $sub_key => $sub_item) {
                $sub_link_url = $sub_item->link->getUrlObject();
                $sub_link = $sub_link_url->toString();
                $variables['menu_links'][$menu_title][$key]['children'][] = ['title' => $sub_item->link->getTitle(), 'url' => $sub_link];
              }
              $second_level = true;

              usort($variables['menu_links'][$menu_title][$key]['children'], function($a, $b) {
                return stripAccents($a['title']) <=> stripAccents($b['title']);
              });
            }
          }

          uasort($variables['menu_links'][$menu_title], function($a, $b) {
            return stripAccents($a['title']) <=> stripAccents($b['title']);
          });

          if ($second_level){
            $variables['menu_links'][$menu_title]['second_level'] = true;
          }
        }
      }
    }
  }
}

/**
 * Custom function stripping accents for sorting purposes
 */
function stripAccents($str) {
  return strtr(mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8'), mb_convert_encoding('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'ISO-8859-1', 'UTF-8'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

/**
 * Implements hook_preprocess_HOOK() for Self-Help Finder paragraph.
 */
function atrium_preprocess_paragraph__self_help_finder(&$variables) {
  $language_id = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $variables['language_id'] = $language_id;
}
