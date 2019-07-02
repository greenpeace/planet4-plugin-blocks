// /* global acf, jQuery */
//
// (function ($) {
//
//   /**
//    * initializeBlock
//    *
//    * Adds custom JavaScript to the block HTML.
//    *
//    * @date    15/4/19
//    * @since   1.0.0
//    *
//    * @param   object $block The block jQuery element.
//    * @param   object attributes The block attributes (only available when editing).
//    * @return  void
//    */
//   var initializeBlock = function ($block) {
//     $block.find('img').doSomething();
//   }
//
//   // Initialize each block on page load (front end).
//   $(document).ready(function () {
//     $('.testimonial').each(function () {
//       initializeBlock($(this));
//     });
//   });
//
//   // Initialize dynamic block preview (editor).
//   if (window.acf) {
//     window.acf.addAction('render_block_preview/type=p4block-covers', initializeBlock);
//   }
//
// })(jQuery);
//
//
// acf.addAction('load', function () {
//   $('#my-element').hide();
//
//   let fields = acf.getFields();
//   console.log(fields);
//
//
//   let cover_type_fields = acf.getFields({name: 'cover_type'});
//   cover_type_fields.forEach(field => {
//     console.log(field);
//     // var rObj = {};
//     // rObj[obj.key] = obj.value;
//     // return rObj;
//   });
//
//
//   let post_blocks = wp.data.select('core/block-editor').getBlocks();
//   post_blocks.forEach(block => {
//     console.log(block);
//   });
//
//   let $cover_type_inputs = jQuery('input[name*="field_5d1374f1262e2"]');
//   // $cover_type_inputs.forEach(block => {
//   //   console.log(block);
//   // });
//
//
// });
//
//
// acf.addAction('ready', function () {
//
//
//   var myCallback = function(field) {
//     console.log(field);
//   };
// acf.addAction('new_field/name=cover_type', myCallback);    // field with key "field_123456"
//
//   //
//   // var myCallback = function(field) {
//   //   console.log(field);
//   // };
// acf.addAction('new_field/name=posts', myCallback);    // field with key "field_123456"
// });
