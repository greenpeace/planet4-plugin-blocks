/* global _, sui, wp */
/* exported SubmenuBlock */

function SubmenuBlock() {
  const me = this;

  me.editAttributeHeading = sui.views.editAttributeField.extend({
    tagName: 'span',
    className: 'block-attribute-wrapper',
    events: {
      'input  input': 'inputChanged',
      'input  textarea': 'inputChanged',
      'change select': 'inputChanged',
      'change input[type="radio"]': 'inputChanged',
      'change input[type="checkbox"]': 'inputChanged'
    },

    inputChanged: function (e) {
      let $el;

      if (this.model.get('attr')) {
        $el = this.$el.find('[name="' + this.model.get('attr') + '"]');
      }

      if ('radio' === this.model.attributes.type || 'p4_radio' === this.model.attributes.type) {
        this.setValue($el.filter(':checked').first().val());
      } else if ('checkbox' === this.model.attributes.type || 'p4_checkbox' === this.model.attributes.type) {
        this.setValue($el.is(':checked'));
      } else if ('range' === this.model.attributes.type) {
        const rangeId = '#' + e.target.id + '_indicator';
        const rangeValue = e.target.value;
        document.querySelector(rangeId).value = rangeValue;
        this.setValue($el.val());
      } else {
        this.setValue($el.val());
      }

      this.triggerCallbacks();
    },

    setValue: function (val) {
      this.model.set('value', val);
    },

    triggerCallbacks: function () {
      const shortcodeName = this.shortcode.attributes.shortcode_tag;
      const attributeName = this.model.get('attr');
      const hookName = [shortcodeName, attributeName].join('.');
      const changed = this.model.changed;
      const collection = _.flatten(_.values(this.views.parent.views._views));
      const shortcode = this.shortcode;

      /*
      * Action run when an attribute value changes on a shortcode
      *
      * Called as `{shortcodeName}.{attributeName}`.
      *
      * @param changed (object)
      *           The update, ie. { "changed": "newValue" }
      * @param viewModels (array)
      *           The collections of views (editAttributeFields)
      *                         which make up this shortcode UI form
      * @param shortcode (object)
      *           Reference to the shortcode model which this attribute belongs to.
      */
      wp.shortcake.hooks.doAction(hookName, changed, collection, shortcode);
    }
  });

  sui.views.editAttributeHeading = me.editAttributeHeading;
}
