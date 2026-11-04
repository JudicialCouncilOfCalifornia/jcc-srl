<?php

namespace Drupal\srl_webform\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElementBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an 'emailorphone' element.
 *
 * @FormElement("emailorphone")
 *
 * @see \Drupal\Core\Render\Element\FormElementBase
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21FormElement.php/class/FormElement
 * @see \Drupal\Core\Render\Element\RenderElement
 * @see https://api.drupal.org/api/drupal/namespace/Drupal%21Core%21Render%21Element
 */
class EmailOrPhone extends FormElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#size' => 60,
      '#process' => [
        [$class, 'processAjaxForm'],
      ],
      '#element_validate' => [
        [$class, 'validateEmailOrPhoneElement'],
      ],
      '#pre_render' => [
        [$class, 'preRenderEmailOrPhoneElement'],
      ],
      '#theme' => 'input__phoneoremail',
      '#theme_wrappers' => ['form_element'],
    ];
  }


  /**
   * Webform element validation handler for #type 'emailorphone'.
   */
  public static function validateEmailOrPhoneElement(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = trim($element['#value']);
    $form_state->setValueForElement($element, $value);

    if ($value !== '') {
      $email_test = \Drupal::service('email.validator')->isValid($value);
      $phone_test = \Drupal::service('srl_webform.usphone.validator')->isValid($value);

      if ((!$email_test && !$phone_test)) {
        $form_state->setError($element, t('The email or phone  address %mail is not valid.', ['%mail' => $value]));
      }
    }
  }

  /**
   * Prepares render element for theme_element().
   */
  public static function preRenderEmailOrPhoneElement(array $element) {
    $element['#attributes']['type'] = 'text';
    Element::setAttributes($element, ['id', 'name', 'value', 'size', 'maxlength', 'placeholder']);
    static::setAttributes($element, ['form-text']);
    return $element;
  }
}
