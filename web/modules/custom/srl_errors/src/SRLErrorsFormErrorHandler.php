<?php

namespace Drupal\srl_errors;

use Drupal\Core\Form\FormElementHelper;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\inline_form_errors\FormErrorHandler;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

/**
 * Extends error handling from inline_form_errors.
 */
class SRLErrorsFormErrorHandler extends FormErrorHandler {

  public function __construct(TranslationInterface $string_translation, RendererInterface $renderer, MessengerInterface $messenger) {
    parent::__construct($string_translation, $renderer, $messenger);
  }

  /**
   * Print errors message without list of fields.
   */
  protected function displayErrorMessages(array $form, FormStateInterface $form_state) {
    // Skip generating inline form errors when opted out.
    if (!empty($form['#disable_inline_form_errors'])) {
      parent::displayErrorMessages($form, $form_state);
      return;
    }

    $error_links = [];
    $errors = $form_state->getErrors();
    // Loop through all form errors and check if we need to display a link.
    foreach ($errors as $name => $error) {
      $form_element = FormElementHelper::getElementByName($name, $form);
      $title = FormElementHelper::getElementTitle($form_element);

      // Only show links to erroneous elements that are visible.
      $is_visible_element = Element::isVisibleElement($form_element);
      // Only show links for elements that have a title themselves or have
      // children with a title.
      $has_title = !empty($title);
      // Only show links for elements with an ID.
      $has_id = !empty($form_element['#id']);

      // Do not show links to elements with suppressed messages. Most often
      // their parent element is used for inline errors.
      if (!empty($form_element['#error_no_message'])) {
        unset($errors[$name]);
      }
      elseif ($is_visible_element && $has_title && $has_id) {
        $error_links[] = Link::fromTextAndUrl($title, Url::fromRoute('<none>', [], ['fragment' => $form_element['#id'], 'external' => TRUE]))->toRenderable();
        unset($errors[$name]);
      }
    }

    // Set normal error messages for all remaining errors.
    foreach ($errors as $error) {
      $this->messenger->addError($error);
    }

    if (!empty($error_links)) {
      $render_array = [
        [
          '#markup' => $this->formatPlural(count($error_links), '1 error has been found. ', '@count errors have been found.'),
        ]
      ];
      $message = $this->renderer->renderInIsolation($render_array);
      $this->messenger->addError($message);
    }
  }

}
