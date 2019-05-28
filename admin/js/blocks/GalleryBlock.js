/*
  global $,
    Backbone,
    image_focus_points
*/
function GalleryBlock(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;
  var gallery_block_app = {
    views: {},
    models: {},
    data: {}
  };

  /**
   * Clean up current view.
   */
  me.cleanup_current_view = function() {
    if ( gallery_block_app.data.CurrentView ) {
      gallery_block_app.data.CurrentView.remove();
      gallery_block_app.data.CurrentView = null;
    }
  };

  /**
   * Post types select box change hook.
   */
  me.gallery_image_change_hook = function ( changed, collection, shortcode ) { // eslint-disable-line no-unused-vars
    let models               = shortcode.attributes.attrs.models;
    let selected_image_ids   = changed.value;
    let initial_focus_points = {};

    models.forEach(function (model) {
      let attr_name = model.get('attr');

      if ( 'gallery_block_focus_points' === attr_name ) {
        let $element  = $('input[name="' + attr_name + '"]');

        let focus_point_details = model.get('value');
        focus_point_details = focus_point_details.replace(/'/g,'"');
        focus_point_details = $.parseJSON(focus_point_details);

        // Filter selected image data
        if ( 'undefined' !== typeof selected_image_ids ) {
          let image_ids_array      = selected_image_ids.toString().split(',');
          let initial_focus_points = {};

          $.each(image_ids_array , function(index, val_data) {
            // Set default focus point.
            if( null !== focus_point_details && focus_point_details.hasOwnProperty(val_data)){
              initial_focus_points[ val_data ] = focus_point_details[val_data];
            } else {
              initial_focus_points[ val_data ] = 'left top';  // Set default focus point.
            }
          });

          initial_focus_points = JSON.stringify( initial_focus_points );
          initial_focus_points = initial_focus_points.replace(/"/g, "'"); // eslint-disable-line quotes

          // Set data to model.
          model.set('value', initial_focus_points);
          $element.val(initial_focus_points);

        } else {
          // Edit gallery block.
          initial_focus_points = focus_point_details ;
        }
      }
    });

    /**
     * Update focus point data to hidden field and main model.
     */
    me.update_focus_points = function (img_id, updated_val) {
      models.forEach(function (model) {
        let attr_name = model.get('attr');

        if ( 'gallery_block_focus_points' === attr_name ) {
          let $element  = $('input[name="' + attr_name + '"]');

          let all_focus_point = model.get('value');
          all_focus_point     = all_focus_point.replace(/'/g,'"');

          let focus_point_data = '[]';

          if ( all_focus_point ) {
            focus_point_data = all_focus_point;
          } else {
            focus_point_data = { img_id : updated_val };
          }

          var json_data = $.parseJSON(focus_point_data);

          if ( json_data.hasOwnProperty( img_id ) ) {
            json_data[ img_id ] = updated_val;
          }

          all_focus_point = JSON.stringify( json_data );
          all_focus_point = all_focus_point.replace( /"/g, "'" ); // eslint-disable-line quotes

          // Set data to model.
          model.set('value', all_focus_point);
          $element.val(all_focus_point);
        }
      });
    };

    // Clean up current view before load.
    me.cleanup_current_view();

    /**
     * After loading of attachement preview, append the focus point field to each attachment.
     */
    $('.shortcake-attachment-preview').each(function() {
      let image_id       = $(this).find('button').attr('data-id');
      let focus_point_id = 'focus_point_' + image_id;
      $(this).find('.thumbnail-details-container').append('<div id="'+focus_point_id+'"></div>');
      let default_focus_point = '';

      if ( initial_focus_points.hasOwnProperty( image_id ) ) {
        default_focus_point = initial_focus_points[ image_id ];
      }

      gallery_block_app.data.focus_point = new gallery_block_app.models.FocusPointModel();

      gallery_block_app.data.focus_point.set({
        'img_id': image_id,
        'focus_points': default_focus_point
      });

      gallery_block_app.data.CurrentView = new gallery_block_app.views.FocusPointView({
        el: '#'+focus_point_id,
        model: gallery_block_app.data.focus_point
      });

      gallery_block_app.data.CurrentView.render();

    });
  };

  /**
   * Gallery block focus point model.
   */
  gallery_block_app.models.FocusPointModel = Backbone.Model.extend({
    defaults: {
      img_id: 'not yet set',
      focus_points: 'no author yet'
    }
  });

  /**
   * Gallery block focus point view.
   */
  gallery_block_app.views.FocusPointView = Backbone.View.extend({

    /**
     * Set focus point dropdown change event and trigger update_focus_point_data function.
     */
    events: {
      'change select[class=img-focus-point]': 'update_focus_point_data'
    },

    /**
     * Reder the html element of focus points selection dropdown.
     */
    render: function() {
      let img_id               = this.$el.attr('id');
      let selected_focus_point = this.model.get('focus_points');

      let img_dimensions = this.$el.parent().find('.dimensions').html();
      let image_type     = this.get_image_type(img_dimensions);

      let focus_points_array = [];
      let focus_points_html  = '';

      focus_points_html += '<hr>';

      if ( 'portrait' === image_type ) {
        focus_points_array = image_focus_points.portrait;
        focus_points_html += '<img src="' + image_focus_points.options_img_grid.portrait + '">';
      } else {
        focus_points_array = image_focus_points.landscape;
        focus_points_html += '<img src="' + image_focus_points.options_img_grid.landscape + '">';
      }

      focus_points_html += '<label>' + image_focus_points.label + ':</label>' +
        '<select name="focus_image_1" class="img-focus-point" id="img-'+img_id+'" placeholder="">';

      $.each(focus_points_array , function(index, val_data) {
        // Set default focus point.
        let selected_option = '';
        if (selected_focus_point === val_data.value) {
          selected_option = 'selected';
        }
        focus_points_html += '<option value="' + val_data.value + '" ' + selected_option + '>' + val_data.label + '</option>';
      });

      focus_points_html += '</select>';

      this.$el.html(focus_points_html);

      this.model.set({
        'focus_points': $('select#img-'+img_id, this.$el).find(':selected').val()
      });
      me.update_focus_points(this.model.get('img_id'), this.model.get('focus_points'));
    },


    /**
     * Update image focus point data to model.
     */
    update_focus_point_data: function(evt) {
      evt.preventDefault();

      let $focus_point = $('select#img-'+this.$el.attr('id'), this.$el);
      let img_id       = this.$el.attr('id').replace ( /[^\d.]/g, '' );

      this.model.set({
        'img_id': img_id,
        'focus_points': $focus_point.find(':selected').val()
      });

      me.update_focus_points(this.model.get('img_id'), this.model.get('focus_points'));
    },

    /**
     * Return Image type(landscape,portrait) on the basis of image width and height.
     */
    get_image_type: function( dimensions ) {
      let image_type = 'landscape';  // Default image type.

      if ( 'undefined' !== typeof dimensions ) {
        dimensions = dimensions.split('×');
        let image_width  = parseInt(dimensions[0], 10);
        let image_height = parseInt(dimensions[1], 10);

        if (image_width > image_height) {
          //landscape image.
          image_type = 'landscape';
        } else if (image_width < image_height) {
          //portrait image.
          image_type = 'portrait';
        } else {
          //image width and height are equal, square image.
          image_type = 'landscape';
        }
      }

      return image_type;
    }
  });
}
