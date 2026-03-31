// Override the default template set
CKEDITOR.addTemplates('default', {
	// The name of sub folder which hold the shortcut preview images of the
	// templates.  Determine base path of drupal installation if any
	// (ckeditor could possibly be loaded w/o drupalSettings).
	imagesPath: ((drupalSettings && drupalSettings.path) ? drupalSettings.path.baseUrl : '/') + 'themes/custom/atrium/ckeditor/',

	// The templates definitions.
	templates: [
    {
      title: 'Detail (Read more)',
      image: 'read-more.png',
      description: 'Contains a trigger and expanded text. Use for exceptions and details not relevant to all readers.',
      template: function () {
        const randomId = +new Date();
        return`<p>Text Before</p>
          <div class="jcc-read-more">
            <span class="jcc-read-more__trigger usa-button usa-button--unstyled" data-a11y-toggle="read-more-${randomId}">
              <span>What is a judgment</span>
              <svg class="icon icon-expand_more" role="img" title="Expand"><use href="#i-expand_more"></use></svg>
            </span>
            <div class="jcc-read-more__content" id="read-more-${randomId}">
              If your bank account is levied, you must act quickly! You have only ten days from the date of the levy to file a claim of exempltion (plus five days if the notice was sent be mail) with the sheriff performing the levy.
            </div>
          </div>
          <p>Text After</p>`
      },
    },
    {
      title: 'Detail - Italic (Read more block)',
      image: 'read-more.png',
      description: 'Contains a trigger and expanded text. Use for exceptions and details not relevant to all readers.',
      template: function () {
        const randomId = +new Date();
        return`<p>Text Before</p>
          <div class="jcc-read-more jcc-read-more--block">
            <button class="jcc-read-more__trigger usa-button usa-button--unstyled" data-a11y-toggle="read-more-${randomId}">
              <span>What is a judgment</span>
              <svg class="icon icon-expand_more" role="img" title="Expand"><use href="#i-expand_more"></use></svg>
            </button>
            <div class="jcc-read-more__content" id="read-more-${randomId}">
              If your bank account is levied, you must act quickly! You have only ten days from the date of the levy to file a claim of exempltion (plus five days if the notice was sent be mail) with the sheriff performing the levy.
            </div>
          </div>
          <p>Text After</p>`
      },
    },
    {
      title: 'Example (Explainer) - One Column',
      image: 'explainer-one.png',
      description: 'Use to highlight real-world examples of concepts.',
      template: function () {
        return `<p>Text Before</p><div class="jcc-explainers-section--has-one-column jcc-explainers-section">
          <div class="jcc-explainers__container">
            <ul class="jcc-explainers">
              <li class="jcc-explainers__item">
                <div class="jcc-explainer">
                  <div class="jcc-explainer__brow">Wage Garnishment</div>
                  <p class="jcc-explainer__content">
                    Someone took money from my paycheck</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <p>Text After</p>`
      }
    },
    {
      title: 'Example (Explainer) - Two Columns',
      image: 'explainer-two.png',
      description: 'Use to highlight real-world examples of concepts.',
      template: function () {
        return `<p>Text Before</p><div class="jcc-explainers-section--has-two-columns jcc-explainers-section">
          <div class="jcc-explainers__container">
            <ul class="jcc-explainers">
              <li class="jcc-explainers__item">
                <div class="jcc-explainer">
                  <div class="jcc-explainer__brow">Wage Garnishment</div>
                  <p class="jcc-explainer__content">
                    Someone took money from my paycheck</p>
                </div>
              </li>
              <li class="jcc-explainers__item">
                <div class="jcc-explainer">
                  <div class="jcc-explainer__brow">Wage Garnishment</div>
                  <p class="jcc-explainer__content">
                    If you bought a car with money that only you earned...</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <p>Text After</p>`
      }
    },
    {
      title: 'Left icon',
      image: 'left-icon.png',
      description: 'Contains a left-align icon and some content.',
      template: function () {
        return `
            <div class="usa-card usa-card--flag jcc-card jcc-card--icon">
                <div class="usa-card__container jcc-border--borderless">
                    <div class="usa-card__body">
                        <h3>Title</h3>
                        <p>text</p>
                    </div>
                    <div class="usa-card__media"></div>
                </div>
            </div>`
      },
    },
    {
      title: 'Callout Block',
      image: 'callout.png',
      description: 'Highlight or repeat important info (appears in right rail).',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-callout  jcc-callout__block">
            <div class="jcc-callout__content">
              In general, after a divorce, you keep your separate property and divide your community property.
            </div>
          </div>
          <p>Text After</p>`
      }
    },
    {
      title: 'Alert - Warning',
      image: 'alert.png',
      description: 'Warn readers to avoid high-stakes errors or address common misconceptions.',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-alert usa-alert usa-alert--warning usa-alert--no-icon active" id="alert-bar">
            <div class="usa-alert__body jcc-alert__body  ">
              <h4 class="usa-alert__heading jcc-alert__heading">Think about safety when serving papers,</h4>
              <div class="usa-alert__text jcc-alert__text">For some people, getting served papers can trigger emotional responses. If you’re using someone who is not a professional server, tell them to keep the interaction brief and not to engage in any conversation about the case. They should remain outside and not enter a private home.</div>
            </div>
          </div>
          <p>Text After</p>`
      }
    },
    {
      title: 'Alert - Error',
      image: 'alert.png',
      description: 'Warn readers to avoid high-stakes errors or address common misconceptions.',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-alert usa-alert usa-alert--error usa-alert--no-icon active" id="alert-bar">
            <div class="usa-alert__body jcc-alert__body  ">
              <h4 class="usa-alert__heading jcc-alert__heading">Think about safety when serving papers,</h4>
              <div class="usa-alert__text jcc-alert__text">For some people, getting served papers can trigger emotional responses. If you’re using someone who is not a professional server, tell them to keep the interaction brief and not to engage in any conversation about the case. They should remain outside and not enter a private home.</div>
            </div>
          </div>
          <p>Text After</p>`
      }
    },
    {
      title: 'Alert - Success',
      image: 'alert.png',
      description: 'Warn readers to avoid high-stakes errors or address common misconceptions.',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-alert usa-alert usa-alert--success usa-alert--no-icon active" id="alert-bar">
            <div class="usa-alert__body jcc-alert__body  ">
              <h4 class="usa-alert__heading jcc-alert__heading">Think about safety when serving papers,</h4>
              <div class="usa-alert__text jcc-alert__text">For some people, getting served papers can trigger emotional responses. If you’re using someone who is not a professional server, tell them to keep the interaction brief and not to engage in any conversation about the case. They should remain outside and not enter a private home.</div>
            </div>
          </div>
          <p>Text After</p>`
      }
    },    {
      title: 'Option List - One Column',
      image: 'option-one.png',
      description: 'Show two equally likely outcomes/actions in an "if then" format.',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-options-list jcc-options-list--has-one-column">
            <div class="jcc-options__container">
              <ul class="jcc-options">
                <li class="jcc-options__item">
                  <div class="jcc-option">
                    <h3>If you're suing a person (or people)</h3>
                    <div class="jcc-option__content">Serve each person you are suing.</div>
                  </div>
                </li>
                <li class="jcc-options__item">
                  <div class="jcc-option">
                    <h3>If you're suing a business or government</h3>
                    <div class="jcc-option__content">Business or agencies have a specific person you must serve, not just any employee. Follow these guidelines to make sure you are serving the right person.</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <p>Text After</p>`
      }
    },
    {
      title: 'Options List - Two Column',
      image: 'option-two.png',
      description: 'Show two equally likely outcomes/actions in an "if then" format.',
      template: function () {
        return `<p>Text Before</p>
          <div class="jcc-options-list jcc-options-list--has-two-columns">
            <div class="jcc-options__container">
              <ul class="jcc-options">
                <li class="jcc-options__item">
                  <div class="jcc-option">
                    <h3>If you're suing a person (or people)</h3>
                    <div class="jcc-option__content">Serve each person you are suing.</div>
                  </div>
                </li>
                <li class="jcc-options__item">
                  <div class="jcc-option">
                    <h3>If you're suing a business or government</h3>
                    <div class="jcc-option__content">Business or agencies have a specific person you must serve, not just any employee. Follow these guidelines to make sure you are serving the right person.</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <p>Text After</p>`
      }
    },
    {
      title: 'List Group',
      image: 'list-group.png',
      description: 'The "List group" component from Courtyard. Each item contains a title and paragraph.',
      html: '<ul class="jcc-list-group">' +
        '<li>' +
        '<p><strong>List item 1</strong></p>' +
        '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>' +
        '</li>' +
        '<li>' +
        '<p><strong>List item 2</strong></p>' +
        '<p>Pellentesque non nunc quis sem malesuada bibendum.</p>' +
        '</li>' +
        '<li>' +
        '<p><strong>List item 3</strong></p>' +
        '<p>Etiam a est in est mollis fringilla tristique gravida leo.</p>' +
        '</li>' +
        '<li>' +
        '<p><strong>List item 4</strong></p>' +
        '<p>Fusce tempor nunc vel tellus suscipit scelerisque sed sit amet neque.</p>' +
        '</li>' +
        '<li>' +
        '<p><strong>List item 5</strong></p>' +
        '<p>Integer commodo ex nec lorem ullamcorper, a tristique neque efficitur.</p>' +
        '</li>' +
        '</ul>'
    },
    {
      title: 'Action List',
      image: 'action-list.png',
      description: 'The "Action list" component from Courtyard. Each item contains a title and brief description, followed by a button link.',
      html: '<ul class="jcc-action-list">' +
        '<li>' +
        '<div class="jcc-action-list__content">' +
        '<p><strong>First brief sentence describing the action</strong></p>' +
        '<p>One to two sentences providing addition details about the action link.</p>' +
        '</div>' +
        '<div class="jcc-action-list__action">' +
        '<a class="usa-button usa-button--secondary" href="#">Button text</a>' +
        '</div>' +
        '</li>' +
        '<li>' +
        '<div class="jcc-action-list__content">' +
        '<p><strong>Second brief sentence describing the action</strong></p>' +
        '<p>One to two sentences providing addition details about the action link.</p>' +
        '</div>' +
        '<div class="jcc-action-list__action">' +
        '<a class="usa-button usa-button--secondary" href="#">Button text longer</a>' +
        '</div>' +
        '</li>' +
        '<li>' +
        '<div class="jcc-action-list__content">' +
        '<p><strong>Third brief sentence describing the action</strong></p>' +
        '<p>One to two sentences providing addition details about the action link.</p>' +
        '</div>' +
        '<div class="jcc-action-list__action">' +
        '<a class="usa-button usa-button--secondary" href="#">Button text</a>' +
        '</div>' +
        '</li>' +
        '<li>' +
        '<div class="jcc-action-list__content">' +
        '<p><strong>Third brief sentence describing the action</strong></p>' +
        '<p>One to two sentences providing addition details about the action link.</p>' +
        '</div>' +
        '<div class="jcc-action-list__action">' +
        '<a class="usa-button usa-button--secondary" href="#">Button text</a>' +
        '</div>' +
        '</li>' +
        '<li>' +
        '<div class="jcc-action-list__content">' +
        '<p><strong>Third brief sentence describing the action</strong></p>' +
        '<p>One to two sentences providing addition details about the action link.</p>' +
        '</div>' +
        '<div class="jcc-action-list__action">' +
        '<a class="usa-button usa-button--secondary" href="#">Button text</a>' +
        '</div>' +
        '</li>' +
        '</ul>'
    },
    {
      title: 'Single "Go to" Link',
      image: 'explainer-one.png',
      description: 'Use to easily link to another page.',
      template: function () {
        return `<p>Text Before</p>
        <div class="jcc-tiles-section jcc-tiles-section--has-two-columns">
          <ul class="jcc-tiles">
            <li class="jcc-tiles__item">
              <div class="jcc-tile jcc-tile--hover">
                <p class="jcc-tile__brow">Go to</p>
                <h3 class="jcc-tile__title">
                  <a href="#">The title of the link tile</a>
                </h3>
                <div class="jcc-tile__icon-link"></div>
              </div>
            </li>
          </ul>
        </div>
        <p>Text After</p>`
      }
    },
    {
      title: 'Multiple "Go to" Links Section',
      image: 'explainer-two.png',
      description: 'A section to add two to three "go to" links to other pages. Requires tile template that follows.',
      template: function () {
        return `<p>Text Before</p>
        <div class="jcc-tiles-section jcc-tiles-section--has-two-columns">
          <ul class="jcc-tiles">
            <li>Insert tile here</li>
            <li>Insert tile here</li>
            <li>Insert tile here</li>
          </ul>
        </div>
        <p>Text After</p>`
      }
    },
    {
      title: '"Go to" Link Tile',
      image: 'explainer-one.png',
      description: 'Tile component for the multiple "Go to" links section.',
      template: function () {
        return `<li class="jcc-tiles__item">
          <div class="jcc-tile jcc-tile--hover">
            <p class="jcc-tile__brow">Go to</p>
            <h3 class="jcc-tile__title">
              <a href="#">The title of the link tile</a>
            </h3>
            <div class="jcc-tile__icon-link"></div>
          </div>
        </li>`
      }
    },
  ]
});

const coreGetTemplates = CKEDITOR.getTemplates;

CKEDITOR.getTemplates = function( name ) {
  const coreTemplates = coreGetTemplates(name);
  coreTemplates.templates.map(function (template) {
    if (typeof template.template === `function`) {
      template.html = template.template()
    }
  });
  return coreTemplates;
};
