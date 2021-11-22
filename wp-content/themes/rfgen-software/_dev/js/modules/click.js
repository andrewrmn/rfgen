'use strict';

const $ = require('jquery');

class Click {

	constructor( options ) {
		//setup any defaults
		this.defaults = {};
		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );
		this.setup();
	}

	setup() {
		if( $('[data-click]').length ) {
			this.events();
		} else {
			return;
		}
	}

	events() {

    const targetOpen = $('*[data-click]');

    targetOpen.on('click touchstart:not(touchmove)', function(event) {
      event.preventDefault();
      let el = $(this);
      let trigger = el.attr('data-click');
      let target = $("#" + trigger);

      if( target.hasClass('is-active') ) {
        target.removeClass('is-active');
        $('body').removeClass('modal-is-active');
        window.location.hash = '';
      } else {
        target.addClass('is-active');
        $('body').addClass('modal-is-active');
      }
    });

    $(document).ready(function(){
      let hash = window.location.hash.replace('#', '');
      $('.modal-wrap').each(function(){
        if( $(this).attr('id') === hash ) {
          $(this).addClass('is-active');
          $('body').addClass('modal-is-active');
        }
      });
    });

	}

}

module.exports = Click;
