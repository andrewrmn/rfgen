'use strict';

const $ = require('jquery');
//const gsap = require('../gsap');

class Rotate {

  constructor( options ) {
    this.defaults = {};
    this.settings = $.extend( true, {}, this.defaults, options );
    this.setup();
  }

  setup() {

    if( $('.js-rotate').length ) {
      this.events();
    } else {
      return;
    }
  }

  events() {

    var circle = $('.js-rotate');
    circle.each(function(){
      let el = $(this);
      let rotate = 360;
      if( el.hasClass('reverse') ) {
        rotate = -360;
      }
      gsap.to(el, 10, {rotation:rotate, transformOrigin:"50% 50%", ease:Linear.easeNone }).repeat( -1 ) ;
    });

  }

}

module.exports = Rotate;
