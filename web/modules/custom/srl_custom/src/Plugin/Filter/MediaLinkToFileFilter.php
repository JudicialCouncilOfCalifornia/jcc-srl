<?php

namespace Drupal\srl_custom\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Forces Linkit media links to resolve to the direct file URL.
 *
 * When an editor links directly to a media entity (rather than embedding it),
 * Linkit stamps the anchor with data-entity-substitution="canonical", which
 * later resolves (via the Linkit filter) to the media entity's canonical
 * page (/media/{mid}). Some roles, including anonymous users, don't have
 * access to that page and are met with a 404/403.
 *
 * This filter runs before the Linkit filter and rewrites
 * data-entity-substitution to "media" on any <a> tag pointing to a media
 * entity (regardless of what substitution value is currently stored), so
 * Linkit's own "media" substitution plugin resolves the link straight to the
 * underlying file URL instead of the media canonical page. It only changes
 * the rendered output, never the stored text.
 *
 * @Filter(
 *   id = "srl_media_link_to_file",
 *   title = @Translation("Force media links to the direct file URL"),
 *   description = @Translation("Rewrites links to media entities so they resolve to the direct file URL instead of the media canonical page."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class MediaLinkToFileFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    if (stripos($text, 'data-entity-type="media"') === FALSE) {
      return new FilterProcessResult($text);
    }

    $dom = Html::load($text);
    $xpath = new \DOMXPath($dom);
    $changed = FALSE;

    foreach ($xpath->query('//a[@data-entity-type="media" and @data-entity-uuid]') as $element) {
      /** @var \DOMElement $element */
      if ($element->getAttribute('data-entity-substitution') !== 'media') {
        $element->setAttribute('data-entity-substitution', 'media');
        $changed = TRUE;
      }
    }

    if (!$changed) {
      return new FilterProcessResult($text);
    }

    return new FilterProcessResult(Html::serialize($dom));
  }

}
