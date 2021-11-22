'use strict';

const $ = require('jquery');

class ScrollTo {


	constructor( options ) {
		//setup any defaults
		this.defaults = {};
		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );
		this.setup();
	}

	setup() {

		if( $('*[data-scroll-to]').length ) {
			this.events();
		} else {
			return;
		}

	}

	events() {
		$('*[data-scroll-to]').on('click touchstart:not(touchmove)', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var trigger = $(this).attr('data-scroll-to'),
			target = $("#" + trigger),
			ss = 1000, //scroll speed
			o = 0; // offset

			if( $(this).attr('data-scroll-speed') ) {
				ss = $(this).attr('data-scroll-speed');
			}
			if( $(this).attr('data-scroll-offset') ) {
				o = $(this).attr('data-scroll-offset');
			}
			$('html, body').animate({
				scrollTop: target.offset().top - o
			}, ss);
		});
	}
}

module.exports = ScrollTo;
