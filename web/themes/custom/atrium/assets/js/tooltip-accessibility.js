(function($, Drupal) {
  'use strict';

  function getTooltipText(rawTooltip) {
    if (!rawTooltip) {
      return '';
    }

    try {
      var settings = JSON.parse(rawTooltip);
      if (!settings || !settings.content) {
        return '';
      }

      // Tooltip content can contain markup; convert to plain text for ARIA.
      var parser = document.createElement('div');
      parser.innerHTML = settings.content;
      return (parser.textContent || parser.innerText || '').trim();
    }
    catch (error) {
      return '';
    }
  }

  Drupal.behaviors.tooltipAccessibility = {
    attach: function(context) {
      $('[data-tooltip]', context).each(function() {
        var element = this;
        var tooltipText = getTooltipText(element.getAttribute('data-tooltip'));

        if (!tooltipText) {
          return;
        }

        var triggerText = (element.textContent || '').trim();
        var ariaLabel = triggerText ? triggerText + '. ' + tooltipText : tooltipText;

        element.setAttribute('aria-label', ariaLabel);

        // Some tooltips are wrapped with nested spans or other focus targets.
        // Mirror the same label onto likely focusable descendants so SR output
        // remains consistent when focus lands on an inner node.
        var nestedTargets = element.querySelectorAll('span, a, button, [tabindex], [role]');
        Array.prototype.forEach.call(nestedTargets, function(target) {
          if (!target.getAttribute('aria-label')) {
            target.setAttribute('aria-label', ariaLabel);
          }
        });
      });
    }
  };

})(jQuery, Drupal);
