/* global p4_vars */
$(document).ready(function () {
  // Block: Content Four Column functionality.
  // Find out how many posts per row are being displayed.
  $('.four-column-content').each( function() {
    var visible_posts = $('.post-column:visible', $(this)).length;

    if (0 === visible_posts % 4) {
      $(this).attr('data-posts_per_row', 4);
    } else if (0 === visible_posts % 3) {
      $(this).attr('data-posts_per_row', 3);
    }
  });

  // Add click event for load more button in Covers blocks.
  $('.btn-load-more-posts-click').off('click').on('click', function () {
    var $row = $('.post-column:hidden', $(this).closest('.container'));
    var posts_per_row = $(this).closest('.four-column-content').data('posts_per_row');

    $(this).blur();
    if ($row.length > 0) {
      $row.slice( 0, posts_per_row ).show('slow');
    }
    if ($row.length <= posts_per_row) {
      $(this).closest('.load-more-posts-button-div').hide('fast');
    }
  });

  // Block: Covers functionality.
  // Find out how many posts per row are being displayed.
  $('.covers-block').each( function() {
    var visible_covers = $('.cover-card-column:visible', $(this)).length;
    if (0 === visible_covers % 3) {
      $(this).attr('data-covers_per_row', 3);
    } else if (0 === visible_covers % 2) {
      $(this).attr('data-covers_per_row', 2);
    }
  });

  // Add click event for load more button in Covers blocks.
  $('.btn-load-more-covers-click').off('click').on('click', function () {
    var $row = $('.cover-card-column:hidden', $(this).closest('.container'));
    var covers_per_row = $(this).closest('.covers-block').data('covers_per_row');

    $(this).blur();
    if ($row.length > 0) {
      $row.slice( 0, covers_per_row ).show('slow');
    }
    if ($row.length <= covers_per_row) {
      $(this).closest('.load-more-covers-button-div').hide('fast');
    }
  });

  // Add click event for load more button in Articles blocks.
  $('.btn-load-more-articles-click').off('click').on('click', function () {
    var $articles = $('.article-list-item.d-none', $(this).closest('.container'));
    var articles_per_click = 3;

    $(this).blur();
    if ($articles.length > 0) {

      // Lazy-load images of hidden articles.
      var $images = $('.article-list-item.d-none img');
      $images.slice(0, articles_per_click).each(function () {
        var image = this;
        image.setAttribute('src', image.getAttribute('data-src'));
        image.onload = function () {
          image.removeAttribute('data-src');
        };
      });
      $articles.slice(0, articles_per_click).removeClass('d-none').fadeOut(0).slideDown('slow');
    }
    $articles = $('.article-list-item.d-none', $(this).closest('.container'));
    if ($articles.length === 0) {
      $(this).closest('.load-more-articles-button-div').hide('fast');
    }
  });

  $('.load-more').off('click').on('click', function(e) {
    e.preventDefault();

    // Append response only to current block.
    const $content = $( this.dataset.content, $(this).closest('section') );
    const next_page = parseInt(this.dataset.page) + 1;
    const total_pages = parseInt( this.dataset.total_pages );
    const url = p4_vars.ajaxurl + `?page=${ next_page }`;
    this.dataset.page = next_page;

    $.ajax({
      url: url,
      type: 'GET',
      data: {
        action:     'load_more',
        args:       this.dataset,
        '_wpnonce': $( '#_wpnonce' ).val(),
      },
      dataType: 'html'
    }).done(function ( response ) {
      // Append the response at the bottom of the results and then show it.
      $content.append( response );
    }).fail(function ( jqXHR, textStatus, errorThrown ) {
      console.log(errorThrown); //eslint-disable-line no-console
    });

    if (next_page === total_pages) {
      $(this).fadeOut();
    }
  });

  // Add click event handler for load more button in Campaign thumbnail blocks.
  $('.btn-load-more-campaigns-click').off('click').on('click', function () {
    var $row = $('.campaign-card-column:hidden', $(this).closest('.container'));
    var covers_per_row = 3;

    $(this).blur();
    if ($row.length > 0) {
      $row.slice( 0, covers_per_row ).show('slow');
    }
    if ($row.length <= covers_per_row) {
      $(this).closest('.load-more-campaigns-button-div').hide('fast');
    }
  });
});
