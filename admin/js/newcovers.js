/* global acf, jQuery */

(function ($) {

  /**
   * initializeBlock
   *
   * Adds custom JavaScript to the block HTML.
   *
   * @date    15/4/19
   * @since   1.0.0
   *
   * @param   object $block The block jQuery element.
   * @param   object attributes The block attributes (only available when editing).
   * @return  void
   */
  var initializeBlock = function ($block) {
    $block.find('img').doSomething();
  }

  // Initialize each block on page load (front end).
  $(document).ready(function () {
    $('.testimonial').each(function () {
      initializeBlock($(this));
    });
  });

  // Initialize dynamic block preview (editor).
  if (window.acf) {
    // window.acf.addAction('render_block_preview/type=p4block-covers', initializeBlock);
  }

})(jQuery);



if (typeof window.acf !== 'undefined' ) {


  acf.addAction('load', function () {

    let fields = acf.getFields();
    console.log(fields);


    let cover_type_fields = acf.getFields({name: 'cover_type'});
    cover_type_fields.forEach(field => {
    });


    let post_blocks = wp.data.select('core/block-editor').getBlocks();
    post_blocks.forEach(block => {
    });

    let $cover_type_inputs = jQuery('input[name*="field_5d1374f1262e2"]');
    // $cover_type_inputs.forEach(block => {
    //   console.log(block);
    // });
  });


  acf.addAction('ready', function () {


    var cover_type_callback = function (field) {
      console.log(field);
      // add click event to this field's button
      field.on('change', 'input', function (e) {
        e.preventDefault();

        let parent_field = acf.getClosestField($(this));
        let posts_field = acf.getField('field_5d137764262e8');

      });
    };


    var posts_callback = function (field) {

      // acf.addAction('hide_field/name=posts', function(field) {
      //   console.log(field);
      // });   // field with key "field_123456"
    };



    acf.addAction('new_field/name=cover_type', cover_type_callback);    // field with key "field_123456"


    // Add callbacks when hiding specific fields.
    // Tags, Posts, Take action pages select boxes will be cleared when hidden.
    acf.addAction('hide_field/name=posts', function (field) {
      field.select2.$el.val(null).trigger('change');
    });

    acf.addAction('hide_field/name=take_action_pages', function (field) {
      field.select2.$el.val(null).trigger('change');
    });

    acf.addAction('hide_field/name=tags', function (field) {
      field.select2.$el.val(null).trigger('change');
    });

    acf.addAction('new_field/name=posts', posts_callback);    // field with key "field_123456"



  });
}
