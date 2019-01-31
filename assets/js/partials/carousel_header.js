/* global Hammer */

$(document).ready(function() {
  'use strict';

  const CarouselHeader = {

    /**
    * Given an active slide return the next slide, wrapping around the end of the carousel.
    *
    * @param {HTMLElement|jQuery} slide A slide in the carousel.
    * @returns {jQuery} A jQuery selection of the next slide.
    */
    nextSlide: function(el) {
      var $el = $(el);
      var $nextSlide = $el.next('.carousel-item');
      // prevAll returns items in reverse DOM order: the first slide is the last element.
      return $nextSlide.length ? $nextSlide : $el.prevAll('.carousel-item').last();
    },

    previousSlide: function(el) {
      var $el = $(el);
      var $previousSlide = $el.prev('.carousel-item');
      return $previousSlide.length ? $previousSlide : $el.nextAll('.carousel-item').last();
    },

    // Update active slide indicators
    switchIndicator: function(index) {
      this.$CarouselIndicators.children().each(function(i, el) {
        $(el).toggleClass('active', i === index);
      });
    },

    setup: function() {
      const me = this;

      me.$CarouselHeaderWrapper = $('#carousel-wrapper-header');

      me.$CarouselIndicators = me.$CarouselHeaderWrapper.find('.carousel-indicators');
      me.$Slides = me.$CarouselHeaderWrapper.find('.carousel-item');

      me.$CarouselHeaderWrapper.find('img').on('load', function () {
        var current_img_src = $(this).get(0).currentSrc;
        var current_bg_img = $(this).parent().css('background-image').replace(/.*\s?url\(['"]?/, '').replace(/['"]?\).*/, '');
        if (current_img_src !== current_bg_img) {
          $(this).parent().css('background-image', 'url(' + $(this).get(0).currentSrc + ')');
        }
      });

      me.$Slides.each(function (i, el) {
        var $slide = $(el);

        // Populate carousel indicators list
        $('<li>')
          .attr('data-target', '#carousel-wrapper-header')
          .attr('data-slide-to', i)
          .toggleClass('active', i === 0)
          .appendTo(me.$CarouselIndicators);

        // Convert the provided image tag into background image styles.
        var $img = $slide.find('img');
        var img_src = $img.get(0).currentSrc || $img.attr('src');
        $slide
          .css('background-image', 'url(' + img_src + ')')
          .css('background-position', $img.data('background-position'));

        // Populate carousel slide index
        $slide.attr('data-slide', i);
      });

      // Bind mouse interaction events
      var clickTargets = '.carousel-control-next';
      if ($('html').attr('dir') == 'rtl') {
        clickTargets += ', .carousel-control-prev';
      }
      me.$CarouselHeaderWrapper.on('click', clickTargets, function(evt) {
        evt.preventDefault();
        me.advanceCarousel();
      });

      me.$CarouselHeaderWrapper.on('click', '.carousel-indicators li', function (evt) {
        evt.preventDefault();
        me.activate($(evt.target).data('slide-to'));
      });

      /* Carousel header swipe on mobile */
      if($('.carousel-header').length > 0) {
        var carousel_element = $('.carousel-header')[0];
        var carousel_head_hammer = new Hammer(carousel_element, { recognizers: [] });
        var hammer = new Hammer.Manager(carousel_head_hammer.element);
        var swipe = new Hammer.Swipe();
        hammer.add(swipe);

        hammer.on('swipeleft', function(){
          me.advanceCarousel();
        });

        hammer.on('swiperight', function(){
          me.backwardsCarousel();
        });

        // Vertical swiping on carousel should scroll the page
        hammer.on('swipeup', function(event){
          var y = $(window).scrollTop();
          event.preventDefault();
          $('html, body').animate({scrollTop: y + 200});
        });
        hammer.on('swipedown', function(event){
          var y = $(window).scrollTop();
          event.preventDefault();
          $('html, body').animate({scrollTop: y - 200});
        });
      }

      switch (me.$CarouselHeaderWrapper.data('block-style')) {
      case 'full-width-classic':
        $.extend(me, FullWidthClassicCarouselHeader);
        me.setupFullWidthClassic();
        break;
      default:
        $.extend(me, ZoomAndSlideToGrayCarouselHeader);
        me.setupPreview();
        break;
      }
    },

    backwardsCarousel: function() {
      var $active = this.$Slides.filter('.active');
      var $previous = this.previousSlide($active);
      this.activate($previous.data('slide'));
    },

    /**
    * Switch to a specific slide.
    *
    * @param {Number} slideIndex The index of a slide in the carousel.
    */
    activate: function(slideIndex) {
      const me = this;
      var $slide = me.$Slides.eq(slideIndex);

      if ($slide.hasClass('active') && !$slide.hasClass('slide-over')) {
        // If the requested slide is active and not transitioning, do nothing.
        return;
      }

      if (me.$CarouselHeaderWrapper.data('block-style') != 'full-width-classic') {
        if ($slide.hasClass('next')) {
          // If the slide being requested is next, transition normally.
          me.advanceCarousel();
          return;
        }
      }

      if (me.activeTransition) {
        clearTimeout(me.activeTransition);
      }

      me.switchIndicator(slideIndex);

      me.$Slides.removeClass('active next slide-over fade-out');
      $slide.addClass('active');
      me.nextSlide($slide).addClass('next');
    }
  };

  const ZoomAndSlideToGrayCarouselHeader = {
    /**
    * This module provides a custom slideshow mechanism for use with the header carousel.
    * The transition behavior in this block is too complex to be easily layered upon the
    * default bootstrap carousel.
    */

    // SLIDE_TRANSITION_SPEED should match $slide-transition-speed in _carousel_header.scss.
    SLIDE_TRANSITION_SPEED: 1000,
    activeTransition: null,

    setupPreview: function() {
      const me = this;

      me.$Slides.each(function (i, el) {
        var $slide = $(el);
        var $nextImg = me.nextSlide($slide).find('img');

        if ($nextImg.length > 0) {
          // Add an element within the slide to hold the next slide preview.
          var $preview = $('<div>')
            .addClass('carousel-preview-wrap')
            .prependTo($slide);

          var next_img_src = $nextImg.get(0).currentSrc || $nextImg.attr('src');
          $('<div>')
            .addClass('carousel-preview')
            .css('background-image', 'url(' + next_img_src + ')')
            .css('background-position', $nextImg.data('background-position'))
            .appendTo($preview);
        }
      });
    },

    /**
    * Advance to the next slide in the carousel.
    */
    advanceCarousel: function() {
      const me = this;
      var $active = me.$Slides.filter('.active');

      me.$Slides.removeClass('next');
      var $next = me.nextSlide($active).addClass('next');

      if (me.activeTransition) {
        // A transition is in progress, so proceed to the next pair of slides
        clearTimeout(me.activeTransition);
        me.activeTransition = null;
        me.$Slides.removeClass('fade-out slide-over active');
        $next.addClass('active');
        me.nextSlide($next).addClass('next');
        me.advanceCarousel();
        return;
      }

      // Transition out the active slide
      $active.addClass('slide-over');

      me.switchIndicator(me.$Slides.index($next));

      // When transition is done, swap out the slides
      me.activeTransition = setTimeout(function beginTransition() {
        $active.addClass('fade-out');
        me.activeTransition = setTimeout(function completeTransition() {
          $active.removeClass('active');
          me.$Slides.removeClass('slide-over fade-out');
          $next.removeClass('next').addClass('active');
          // Ensure the new upcoming slide has .next
          me.nextSlide($next).addClass('next');
          me.activeTransition = null;
        }, me.SLIDE_TRANSITION_SPEED / 2);
      }, me.SLIDE_TRANSITION_SPEED);
    },
  };

  const FullWidthClassicCarouselHeader = {
    /**
    * This module provides a custom slideshow mechanism for use with the header carousel.
    * The transition behavior in this block is too complex to be easily layered upon the
    * default bootstrap carousel.
    */

    // SLIDE_TRANSITION_SPEED should match $slide-transition-speed in _carousel_header.scss.
    SLIDE_TRANSITION_SPEED: 1000,
    activeTransition: null,

    positionIndicators: function() {
      var $indicators = $('.carousel-indicators');
      var $header = $('.main-header');
      var rightSide = (window.matchMedia('(min-width: 991px)').matches && $('html').attr('dir') == 'rtl')
                      || (window.matchMedia('(min-width: 768px) and (max-width: 991px)').matches && $('html').attr('dir') != 'rtl');

      if (window.matchMedia('(min-width: 768px)').matches) {
        if (rightSide) {
          var rightOffset = $header.offset().left + $header.width();
          var indicatorsRight = $(window).width() - rightOffset;
          $indicators.css('right', indicatorsRight + 'px')
            .css('left', '')
            .css('margin-left', '0')
            .css('margin-right', '-3px');
        } else {
          var leftOffset = $header.offset().left;
          $indicators.css('left', leftOffset + 'px')
            .css('right', '')
            .css('margin-right', '0')
            .css('margin-left', '-3px');
        }
      } else {
        $indicators.css('right', '');
        $indicators.css('left', '');
      }
    },

    setupFullWidthClassic: function() {
      const me = this;
      me.$CarouselHeaderWrapper.on('click', '.carousel-control-prev', function(evt) {
        evt.preventDefault();
        me.backwardsCarousel();
      });

      me.positionIndicators();
      $(window).on('resize', me.positionIndicators);
    },

    backwardsCarousel: function() {
      const me = this;

      var $active = me.$Slides.filter('.active');
      me.$Slides.removeClass('next');
      var $prev = me.previousSlide($active).addClass('next');

      if (me.activeTransition) {
        // A transition is in progress, so proceed to the next pair of slides
        clearTimeout(me.activeTransition);
        me.activeTransition = null;
        me.$Slides.removeClass('fade-out slide-over active');
        $prev.addClass('active');
        me.previousSlide($prev).addClass('next');
        me.advanceCarousel();
        return;
      }

      me.switchIndicator(me.$Slides.index($prev));

      // When transition is done, swap out the slides
      me.activeTransition = setTimeout(function beginTransition() {
        $active.addClass('fade-out');
        me.activeTransition = setTimeout(function completeTransition() {
          $active.removeClass('active');
          me.$Slides.removeClass('fade-out');
          $prev.removeClass('next').addClass('active');
          // Ensure the new upcoming slide has .next
          me.nextSlide($prev).addClass('next');
          me.activeTransition = null;
        }, me.SLIDE_TRANSITION_SPEED / 2);
      }, me.SLIDE_TRANSITION_SPEED);
    },

    /**
    * Advance to the next slide in the carousel.
    */
    advanceCarousel: function() {
      const me = this;
      var $active = me.$Slides.filter('.active');
      me.$Slides.removeClass('next');
      var $next = me.nextSlide($active).addClass('next');

      if (me.activeTransition) {
        // A transition is in progress, so proceed to the next pair of slides
        clearTimeout(me.activeTransition);
        me.activeTransition = null;
        me.$Slides.removeClass('fade-out slide-over active');
        $next.addClass('active');
        me.nextSlide($next).addClass('next');
        me.advanceCarousel();
        return;
      }

      // Transition out the active slide
      $active.addClass('slide-over');

      me.switchIndicator(me.$Slides.index($next));

      // When transition is done, swap out the slides
      me.activeTransition = setTimeout(function beginTransition() {
        $active.addClass('fade-out');
        me.activeTransition = setTimeout(function completeTransition() {
          $active.removeClass('active');
          me.$Slides.removeClass('slide-over fade-out');
          $next.removeClass('next').addClass('active');
          // Ensure the new upcoming slide has .next
          me.nextSlide($next).addClass('next');
          me.activeTransition = null;
        }, me.SLIDE_TRANSITION_SPEED / 2);
      }, me.SLIDE_TRANSITION_SPEED);
    },
  };

  CarouselHeader.setup();
});
