/* global wp */

function WPShortcakeHooksSetup(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;

  // Define shortcake hooks for blocks fields and blocks views.
  if ('undefined' !== typeof (wp.shortcake)) {

    /**
     * Attach shortcake hooks for block fields.
     */
    me.attach_hooks = function() {
      if (!p4BlocksUI.hooks_defined) {
        p4BlocksUI.hooks_defined = true;
        wp.shortcake.hooks.addAction('shortcake_newcovers.cover_type', p4BlocksUI.blocks['NewCoversBlock'].type_of_cover_change_hook);
        wp.shortcake.hooks.addAction('shortcake_newcovers.tags', p4BlocksUI.blocks['NewCoversBlock'].tags_change_hook);
        wp.shortcake.hooks.addAction('shortcake_newcovers.post_types', p4BlocksUI.blocks['NewCoversBlock'].post_types_change_hook);
        wp.shortcake.hooks.addAction('shortcake_newcovers.posts', p4BlocksUI.blocks['NewCoversBlock'].posts_select_change_hook);

        wp.shortcake.hooks.addAction('shortcake_articles.posts', p4BlocksUI.blocks['ArticlesBlock'].posts_select_change_hook);
        wp.shortcake.hooks.addAction('shortcake_articles.post_types', p4BlocksUI.blocks['ArticlesBlock'].page_types_change_hook);
        wp.shortcake.hooks.addAction('shortcake_articles.tags', p4BlocksUI.blocks['ArticlesBlock'].page_types_change_hook);
        wp.shortcake.hooks.addAction('shortcake_articles.read_more_link', p4BlocksUI.blocks['ArticlesBlock'].read_more_change_hook);

        wp.shortcake.hooks.addAction('shortcake_social_media.embed_type', p4BlocksUI.blocks['SocialMediaBlock'].embed_type_change_hook);

        wp.shortcake.hooks.addAction('shortcake_gallery.multiple_image', p4BlocksUI.blocks['GalleryBlock'].gallery_image_change_hook);
      }

      // There may be multiple social media embeds on a page; fields need initializing separately for each one.
      p4BlocksUI.blocks['SocialMediaBlock'].initialize_fields();
    };

    // Attach hooks when rendering a new p4 block.
    wp.shortcake.hooks.addAction('shortcode-ui.render_new', function (shortcode) {
      me.attach_hooks();

      var shortcode_tag = shortcode.get('shortcode_tag');
      if ('shortcake_columns' === shortcode_tag) {
        p4BlocksUI.blocks['ColumnsBlock'].render_new(shortcode);
      }

      if ('shortcake_carousel_header' === shortcode_tag) {
        p4BlocksUI.blocks['CarouselHeaderBlock'].render_new();
      }
    });

    // Trigger hooks when shortcode renders an existing p4 block.
    wp.shortcake.hooks.addAction('shortcode-ui.render_edit', function (shortcode) {
      me.attach_hooks();

      var shortcode_tag = shortcode.get('shortcode_tag');
      var block_name = shortcode_tag.replace('shortcake_', '');
      if (['shortcake_articles', 'shortcake_newcovers'].includes(shortcode_tag)) {

        var requests = shortcode.get('ajax_requests');

        if (null !== requests) {

          // Block ui / shortcake block view until all fields are populated.
          var $block_div = $('.shortcode-ui-edit-' + shortcode_tag);
          $block_div.addClass('not-clickable');
          $block_div.prev().prepend('<span class="spinner is-active" id="bl_loader"></span>' +
            '<span id="bl_loading_span">Populating block\'s fields..</span>');
          $block_div.animate({opacity: 0.5});

          // Add a hook to unblock shortcake block's view when all ajax requests have been completed.
          Promise.all(requests).then(function () {
            $block_div.animate({opacity: 1});
            $block_div.removeClass('not-clickable');
            $('#bl_loader').removeClass('is-active');
            $('#bl_loading_span').remove();
            shortcode.unset('ajax_requests');
            p4BlocksUI.initialize_view_fields(block_name);
          });
        }
      }

      if ('shortcake_columns' === shortcode_tag) {
        p4BlocksUI.blocks['ColumnsBlock'].render_edit(shortcode);
      }

      if ('shortcake_carousel_header' === shortcode_tag) {
        p4BlocksUI.blocks['CarouselHeaderBlock'].render_edit();
      }
    });
  }
}
