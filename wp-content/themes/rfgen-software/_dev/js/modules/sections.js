'use strict';

const $ = require('jquery');

class Sections {

  constructor( options ) {
    //setup any defaults
    this.defaults = {};
    //merge options with defaults
    this.settings = $.extend( true, {}, this.defaults, options );
    this.setup();
  }

  setup() {
    if( $('.js-page-section').length ) {
      this.events();
    } else {
      return;
    }
  }

  events() {
    var section = $('.js-page-section'),
    w = $(window),
    offsetCount = 134;

    function checkSection() {
      section.each(function(){
        var el = $(this),
        topPos = el.offset().top - offsetCount,
        bottomPos = el.offset().top + el.innerHeight(),
        elId = el.attr('id'),
        navLinks = $('.page-navigation a');

        if ( w.scrollTop() >= topPos && w.scrollTop() < (bottomPos - offsetCount) ){
          if( !el.hasClass('active-section') ){
            el.addClass('active-section');

            navLinks.each(function(){
              var nl = $(this);
              if( nl.attr('data-scroll-to') == elId ) {
                navLinks.removeClass('is-active');
                nl.addClass('is-active');
              }
            });
          }
        } else {
          if( el.hasClass('active-section') ){
            el.removeClass('active-section');
          }
        }
      });
    }

    function throttle(fn, wait) {
      var time = Date.now();
      return function() {
        if ((time + wait - Date.now()) < 0) {
          fn();
          time = Date.now();
        }
      };
    }

    window.addEventListener('scroll', throttle(checkSection, 100));
    window.addEventListener('resize', checkSection);

    $(document).ready(function(){
      $(window).trigger('resize');
    });

  }

}

module.exports = Sections;
