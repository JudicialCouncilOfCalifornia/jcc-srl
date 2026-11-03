<?php

namespace Drupal\srl_custom\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Attaches the tooltip library to text that contains tooltip markup.
 *
 * Replaces the "tooltip" filter of the contrib tooltip module, which depends
 * on CKEditor 4. Content keeps its <span data-tooltip="..."> markup; editors
 * add it through Source editing.
 *
 * @Filter(
 *   id = "srl_tooltip",
 *   title = @Translation("Tooltip"),
 *   description = @Translation("Shows the data-tooltip text of an element as a tooltip on hover."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class TooltipFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (stristr($text, 'data-tooltip') !== FALSE) {
      // Same normalization the contrib filter applied, so output is unchanged.
      $result->setProcessedText(Html::serialize(Html::load($text)));
      $result->addAttachments(['library' => ['srl_custom/tooltip']]);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Use Tooltip to display HTML when user hover selected text.');
  }

}
