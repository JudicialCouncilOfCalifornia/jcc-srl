<?php

namespace Drupal\srl_custom;

/**
 * Gives "read more" components inserted from editor templates unique ids.
 *
 * The CKEditor 5 templates are static HTML, so they ship a placeholder id
 * ("read-more-new") on the trigger (data-a11y-toggle) and on the panel (id).
 * Each trigger/panel pair is rewritten to its own unique id on save.
 */
class ReadMoreIds {

  /**
   * The placeholder id used by the templates.
   */
  public const PLACEHOLDER = 'read-more-new';

  /**
   * Replaces each placeholder trigger/panel pair with a unique id.
   *
   * @param string $html
   *   The markup to process.
   *
   * @return string
   *   The markup with unique ids; unchanged if it has no placeholder.
   */
  public static function uniquify(string $html): string {
    if (!str_contains($html, self::PLACEHOLDER)) {
      return $html;
    }
    $pattern = '/(data-a11y-toggle=")' . self::PLACEHOLDER . '(".*?\bid=")' . self::PLACEHOLDER . '"/s';
    return preg_replace_callback($pattern, function (array $m) {
      $id = 'read-more-' . bin2hex(random_bytes(6));
      return $m[1] . $id . $m[2] . $id . '"';
    }, $html);
  }

}
