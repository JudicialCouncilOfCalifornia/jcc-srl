(function (Drupal, Popper, once) {
  const onceName = 'tooltip';
  const triggerSelector = '[data-tooltip]';
  const tooltipSelector = '[data-tooltip-id]';
  const tooltipHoverTimeout = 250;

  /**
   * Enable tooltip elements.
   */
  Drupal.behaviors.tooltip = {
    attach: function (context, settings) {
      /**
       * Generate tooltip from arbitrary content.
       *
       * @param {string} content
       * @param {boolean} displayArrow
       *
       * @returns DOMElement|null
       */
      function createTooltipFromElement(element) {
        let attr = {};
        let content = '';
        
        // Get values and catch errors.
        try {
          attr = JSON.parse(element.dataset.tooltip || '{}');
          content = attr.content || element.title;
        } catch(e) {
          return;
        }

        if (!content.length) {
          return;
        }

        let displayArrow = Boolean(attr.arrow ?? true);
        let uniqueId = Date.now() + '-' + Math.random().toString().slice(2, 4);
        let tooltip = document.createElement('div');
        tooltip.classList.add('tooltip');
        tooltip.dataset.tooltipId = uniqueId;
        tooltip.innerHTML = content;
        if (displayArrow) {
          tooltip.innerHTML += '<div data-popper-arrow></div>';
        }
        document.body.appendChild(tooltip);

        // Set block ID in [data-tooltip] on element in case of Ajax refresh,
        // so tooltip will be instanciated later.
        attr.block = uniqueId;
        delete attr.block;
        element.dataset.tooltip = JSON.stringify(attr);

        // Prevent overlapping title.
        delete element.title;

        return tooltip;
      };

      /**
       *
       * @param {*} tooltip
       */
      function instanciateTooltip(element, tooltip) {
        // Otherwise, not displayed correctly is inside [.block__content].
        // @see \Drupal\tooltip\TooltipBlockBase::preRender();
        let arrow = tooltip.querySelector('[data-popper-arrow]');
        if (arrow) {
          tooltip.appendChild(arrow);
        }

        // Set attribute to match Popper's documentation.
        // @see https://popper.js.org/docs/v2/tutorial/#arrow
        let attr = JSON.parse(element.dataset.tooltip || '{}');
        element.dataset.popperPlacement = attr.placement ?? 'top';

        // Popper setting.
        let options = {};
        options.placement = element.dataset.popperPlacement;

        // Default offset.
        options.modifiers = attr.modifiers || [];
        if (!options.modifiers.filter(_modifier => _modifier.name == 'offset')) {
          options.modifiers.push({ name: 'offset', options: { offset: [0, 10] } });
        }

        // Render tooltip.
        const popperInstance = Popper.createPopper(element, tooltip, options);

        // Hide tooltip by default.
        tooltip.classList.add('visually-hidden');

        // Show tooltip content if it was hidden.
        tooltip.querySelectorAll('.tooltip.visually-hidden').forEach(function (tooltipContent) {
          tooltipContent.classList.remove('visually-hidden');
        });

        // Determine if mouse is over tooltip to keep it displayed.
        tooltip.addEventListener('mouseenter', function () {
          this.dataset.reading = true;
        });
        tooltip.addEventListener('mouseleave', function () {
          delete this.dataset.reading;
          this.classList.add('visually-hidden');
        });

        // Show tooltip on focus.
        element.addEventListener('mouseenter', function () {
          tooltip.classList.remove('visually-hidden');
          popperInstance.update();
        });
        element.addEventListener('focus', function () {
          tooltip.classList.remove('visually-hidden');
          popperInstance.update();
        });

        // Hide tooltip on focus out.
        element.addEventListener('mouseleave', function () {
          setTimeout(function () {
            // Keep tooltip alive if mouse on top.
            if (!Boolean(tooltip.dataset.reading)) {
              tooltip.classList.add('visually-hidden');
            }
          }, tooltipHoverTimeout);
        });
        element.addEventListener('blur', function () {
          setTimeout(function () {
            // Keep tooltip alive if mouse on top.
            if (!Boolean(tooltip.dataset.reading)) {
              tooltip.classList.add('visually-hidden');
            }
          }, tooltipHoverTimeout);
        });
      };

      // Read and instanciate Tooltips.
      once(onceName, triggerSelector, context).forEach(async function (element) {
        // Get block if placed on the same page OR create tooltip content element.
        let attr = JSON.parse(element.dataset.tooltip || '{}');
        let tooltipQuerySelector = '[data-tooltip-id="' + attr.block + '"]';
        let tooltip = context.querySelector(tooltipQuerySelector);

        // Retrieve tooltip's content dynamically.
        // @see tooltip.routing.yml
        if (!tooltip && Boolean(attr.block)) {
          const ajaxObject = Drupal.ajax({ url: Drupal.url(`tooltip/block/${attr.block}`) });
          ajaxObject.success = function (response, status) {
            let attr = JSON.parse(element.dataset.tooltip || '{}');
            attr.content = response.content;
            element.dataset.tooltip = JSON.stringify(attr);
            tooltip = createTooltipFromElement(element);
            if (tooltip) {
              instanciateTooltip(element, tooltip);
            } else {
              // Remove attributes for a cleaner frontend.
              delete element.dataset.tooltip;
            }
          };
          ajaxObject.execute();
          return;
        }

        if (!tooltip) {
          tooltip = createTooltipFromElement(element);
        }

        if (tooltip) {
          // Accessibility things.
          if (tooltip.dataset.tooltipId) {
            element.setAttribute('tabindex', '0');
            element.setAttribute('aria-describedby', tooltip.dataset.tooltipId);
            tooltip.setAttribute('id', tooltip.dataset.tooltipId);
            tooltip.setAttribute('role', 'tooltip');
          }

          instanciateTooltip(element, tooltip);
        } else {
          // Remove attributes for a cleaner frontend.
          delete element.dataset.tooltip;
        }
      });

      // Hide tooltip by default, if necessary.
      once(onceName, tooltipSelector, context).forEach(function (element) {
        if (!element.classList.contains('visually-hidden')) {
          // element.classList.add('visually-hidden');
        }
      });
    },
    detach(context, settings, trigger) {
      if (trigger === 'unload') {
        once.remove(onceName, triggerSelector, context);
        once.remove(onceName, tooltipSelector, context);
      }
    },
  }

})(Drupal, Popper, once);
