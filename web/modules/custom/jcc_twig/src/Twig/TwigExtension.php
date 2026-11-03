<?php

namespace Drupal\jcc_twig\Twig;

use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Template\TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Additional twig extensions.
 */
class TwigExtension extends AbstractExtension {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs the Twig extension.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   */
  public function __construct(LanguageManagerInterface $language_manager, DateFormatterInterface $date_formatter) {
    $this->languageManager = $language_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('remove_empty', [$this, 'removeEmpty']),
      new TwigFilter('clean_unique_id', [$this, 'uniqueId']),
      new TwigFilter('i18n_format_date', [$this, 'formatDate'], ['needs_environment' => TRUE]),
      new TwigFilter('remove_html_comments', [$this, 'removeHtmlComments']),
      new TwigFilter('unescape', [$this, 'unescape']),
      new TwigFilter('auto_convert_urls', [$this, 'autoConvertUrls']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('term_field_from_id', [$this, 'termFieldFromId'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Remove empty items from an array.
   */
  public function removeEmpty($array) {
    if (is_array($array)) {
      $array = array_filter($array);
    }

    return $array;
  }

  /**
   * Prepares a string for use as a valid HTML ID and guarantees uniqueness.
   *
   * See Html::getUniqueId()
   */
  public function uniqueId($id) {
    return Html::getUniqueId($id);
  }

  /**
   * Render a custom date format with Twig.
   *
   * Use the internal helper "format_date" to render the date
   * using the current language for texts.
   *
   * @param \Drupal\Core\Template\TwigEnvironment $env
   *   A Twig environment instance.
   * @param int|string|DateTime $date
   *   A string, integer timestamp or DateTime object to convert.
   * @param string $type
   *   (optional) The format to use, one of:
   *   - One of the built-in formats: 'short', 'medium',
   *     'long', 'html_datetime', 'html_date', 'html_time',
   *     'html_yearless_date', 'html_week', 'html_month', 'html_year'.
   *   - The name of a date type defined by a date format config entity.
   *   - The machine name of an administrator-defined date format.
   *   - 'custom', to use $format.
   *   Defaults to 'medium'.
   * @param string $format
   *   (optional) If $type is 'custom', a PHP date format string suitable for
   *   input to date(). Use a backslash to escape ordinary text, so it does not
   *   get interpreted as date format characters.
   * @param string|null $timezone
   *   (optional) Time zone identifier, as described at
   *   http://php.net/manual/timezones.php Defaults to the time zone used to
   *   display the page.
   * @param string|null $langcode
   *   (optional) Language code to translate to. NULL (default) means to use
   *   the content language for the page.
   *
   * @return string|null
   *   A translated date string in the requested format. Since the format may
   *   contain user input, this value should be escaped when output.
   */
  public function formatDate(TwigEnvironment $env, $date, $type = 'medium', $format = '', $timezone = NULL, $langcode = NULL) {
    // Use user's timezone so date/time is rendered without timezone adjustment.
    $account = \Drupal::currentUser();
    $timezone = $account->getTimeZone() ?? 'America/Los_Angeles';

    // Twig 3.9+ exposes the converter on CoreExtension; Twig 2 only has the
    // global function.
    $core = $env->getExtension(CoreExtension::class);
    $date = method_exists($core, 'convertDate') ? $core->convertDate($date) : twig_date_converter($env, $date);
    if (empty($langcode)) {
      $langcode = $this->getLanguageManager()
        ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
        ->getId();
    }

    if ($date instanceof \DateTime) {
      return $this->getDateFormatter()->format($date->getTimestamp(), $type, $format, $timezone, $langcode);
    }
    return NULL;
  }

  /**
   * Removes html comments from string.
   */
  public function removeHtmlComments($string) {
    $output = preg_replace('/<!--(.|\s)*?-->/', '', $string);
    return $output;
  }

  /**
   * Gets a field value from a taxonomy term.
   */
  public function termFieldFromId($id, $field_name) {
    if (!is_numeric($id)) {
      return '';
    }

    $field = '';
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($id);

    if (!empty($term)) {
      $field = $term->{$field_name}->value;
    }

    return $field;
  }

  /**
   * Decodes all HTML entities including numerical ones to regular UTF-8 bytes.
   */
  public function unescape($value) {
    return Html::decodeEntities($value);
  }

  /**
   * Finds different occurrences of urls or email addresses in a string.
   */
  public function autoConvertUrls($string) {
    $pattern = '/(href="|src=")?([-a-zA-Zа-яёА-ЯЁ0-9@:%_\+.~#?&\*\/\/=]{2,256}\.[a-zа-яё]{2,4}\b(\/?[-\p{L}0-9@:%_\+.~#?&\*\/\/=\(\),;]*)?)/u';
    $stringFiltered = preg_replace_callback($pattern, [$this, 'callbackReplace'], $string);

    return $stringFiltered;
  }

  /**
   * Replace text from autoConvertUrls.
   */
  public function callbackReplace($matches) {
    if ($matches[1] !== '') {
      // Don't modify existing <a href="">links</a> and <img src="">.
      return $matches[0];
    }

    $url = $matches[2];
    $urlWithPrefix = $matches[2];

    if (strpos($url, '@') !== FALSE) {
      $urlWithPrefix = 'mailto:' . $url;
    }
    elseif (strpos($url, 'https://') === 0) {
      $urlWithPrefix = $url;
    }
    elseif (strpos($url, 'http://') !== 0) {
      $urlWithPrefix = 'http://' . $url;
    }

    // Ignore tailing special characters.
    if (preg_match("/^(.*)(\.|\,|\?)$/", $urlWithPrefix, $matches)) {
      $urlWithPrefix = $matches[1];
      $url = substr($url, 0, -1);
      $punctuation = $matches[2];
    }
    else {
      $punctuation = '';
    }

    return '<a href="' . $urlWithPrefix . '">' . $url . '</a>' . $punctuation;
  }

  /**
   * Returns the language manager service.
   */
  protected function getLanguageManager() {
    return $this->languageManager;
  }

  /**
   * Provides a service to handle various date related functionality.
   */
  protected function getDateFormatter() {
    return $this->dateFormatter;
  }

}
