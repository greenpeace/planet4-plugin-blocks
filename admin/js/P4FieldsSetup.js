/* global wp, shortcodeUIFieldData */
/* exported P4FieldsSetup */

function P4FieldsSetup() {
  if ('undefined' !== typeof (wp.shortcake)) {

    shortcodeUIFieldData.p4_select = {
      encode: false,
      template: 'shortcode-ui-field-p4-select',
      view: 'editAttributeHeading'
    };
    shortcodeUIFieldData.p4_checkbox = {
      encode: false,
      template: 'shortcode-ui-field-p4-checkbox',
      view: 'editAttributeHeading'
    };
    shortcodeUIFieldData.p4_radio = {
      encode: false,
      template: 'shortcode-ui-field-p4-radio',
      view: 'editAttributeHeading'
    };

    // break submenu attribute groups into rows
    const addTags = function () {
      $('.shortcode-ui-attribute-heading2').parent().before('<p></p>');
      $('.shortcode-ui-attribute-heading3').parent().before('<p></p>');
    };
    wp.shortcake.hooks.addAction('shortcode-ui.render_edit', addTags);
    wp.shortcake.hooks.addAction('shortcode-ui.render_new', addTags);
  }
}
