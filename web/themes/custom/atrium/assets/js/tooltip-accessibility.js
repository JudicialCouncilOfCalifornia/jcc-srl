(function($, Drupal) {
  'use strict';

  var A11Y_DESC_CLASS = 'tooltip-a11y-description';
  var LIVE_REGION_ID = 'tooltip-a11y-live-region';

  function textFromMarkup(markup) {
    if (!markup) { return ''; }
    var parser = document.createElement('div');
    parser.innerHTML = markup;
    return (parser.textContent || parser.innerText || '').trim();
  }

  function getTooltipText(element) {
    var rawTooltip = element.getAttribute('data-tooltip');
    if (!rawTooltip) {
      return (element.getAttribute('title') || '').trim();
    }
    try {
      var settings = JSON.parse(rawTooltip);
      if (!settings) { return ''; }
      if (settings.content) { return textFromMarkup(settings.content); }
      if (settings.block) {
        var tooltipNode = document.querySelector('[data-tooltip-id="' + settings.block + '"]');
        if (tooltipNode) {
          return (tooltipNode.textContent || tooltipNode.innerText || '').trim();
        }
      }
      return (element.getAttribute('title') || '').trim();
    }
    catch (e) { return ''; }
  }

  function ensureLiveRegion() {
    var liveRegion = document.getElementById(LIVE_REGION_ID);
    if (liveRegion) { return liveRegion; }
    liveRegion = document.createElement('div');
    liveRegion.id = LIVE_REGION_ID;
    liveRegion.className = 'visually-hidden';
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    document.body.appendChild(liveRegion);
    return liveRegion;
  }

  function appendDescribedBy(element, id) {
    var current = (element.getAttribute('aria-describedby') || '').trim();
    if (!current.length) {
      element.setAttribute('aria-describedby', id);
      return;
    }
    var ids = current.split(/\s+/);
    if (ids.indexOf(id) === -1) {
      ids.push(id);
      element.setAttribute('aria-describedby', ids.join(' '));
    }
  }

  function applyAriaLabel(element, attempts) {
    var tooltipText = getTooltipText(element);

    if (!tooltipText && attempts < 6) {
      setTimeout(function() { applyAriaLabel(element, attempts + 1); }, 300);
      return;
    }
    if (!tooltipText) { return; }

    element.setAttribute('data-tooltip-a11y', 'applied');

    // Dedicated hidden description node linked via aria-describedby.
    // SR reads trigger text naturally then the description once.
    var descId = element.getAttribute('data-tooltip-a11y-id');
    if (!descId) {
      descId = 'tooltip-a11y-desc-' + Math.random().toString(36).slice(2, 10);
      element.setAttribute('data-tooltip-a11y-id', descId);
    }

    var describedByNode = document.getElementById(descId);
    if (!describedByNode) {
      describedByNode = document.createElement('span');
      describedByNode.id = descId;
      describedByNode.className = 'visually-hidden ' + A11Y_DESC_CLASS;
      element.parentNode.insertBefore(describedByNode, element.nextSibling);
    }
    describedByNode.textContent = tooltipText;
    appendDescribedBy(element, descId);

    if (!element.hasAttribute('tabindex')) {
      element.setAttribute('tabindex', '0');
    }

    if (element.hasAttribute('data-tooltip-a11y-events')) { return; }
    element.setAttribute('data-tooltip-a11y-events', 'bound');

    var liveRegion = ensureLiveRegion();

    var announce = function() {
      liveRegion.textContent = '';
      setTimeout(function() { liveRegion.textContent = tooltipText; }, 10);
    };

    element.addEventListener('focus', announce);
    element.addEventListener('mouseenter', announce);
    element.addEventListener('mouseleave', function() {
      liveRegion.textContent = '';
    });

    element.addEventListener('keydown', function(event) {
      if (event.key !== 'Escape' && event.key !== 'Esc') { return; }
      event.preventDefault();
      event.stopPropagation();
      liveRegion.textContent = '';
      var ids = (element.getAttribute('aria-describedby') || '').split(/\s+/);
      for (var i = 0; i < ids.length; i++) {
        var popup = ids[i] ? document.getElementById(ids[i]) : null;
        if (popup && popup.hasAttribute('data-tooltip-id')) {
          popup.classList.add('visually-hidden');
          break;
        }
      }
      element.blur();
    });
  }

  Drupal.behaviors.tooltipAccessibility = {
    attach: function(context) {
      $('[data-tooltip]', context).each(function() {
        applyAriaLabel(this, 0);
      });
    }
  };

})(jQuery, Drupal);
