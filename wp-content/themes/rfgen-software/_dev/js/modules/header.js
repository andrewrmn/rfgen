'use strict';

const $ = require('jquery');

class Header {

	constructor( options ) {

		var el = $('.site-header'),
			w = $(window),
			d = $(document);

		d.scroll(function() {
			if (w.scrollTop() >= 40) {
				if( !el.hasClass('is-sticky') ){
					el.addClass('is-sticky');
				}
			} else {
				if( el.hasClass('is-sticky') ){
					el.removeClass('is-sticky');
				}
			}
		});


		$('.js-menu-trigger').click(function(e){
			e.preventDefault();
			if( $('body').hasClass('menu-is-open') ) {
				$('body').removeClass('menu-is-open');
			} else {
				$('body').addClass('menu-is-open');
			}
		});

		const subnav = $('.menu-item-has-children > a');
		$('.navigation-drawer .sub-menu').hide();
		$('.navigation-drawer .current-menu-item').addClass('is-active');
		$('.navigation-drawer .current-menu-item .sub-menu').show();

		subnav.click(function(e){
			e.preventDefault();
			let el = $(this);
			let elPar = el.parent('li');
			let elTar = elPar.find('.sub-menu');

			if( elPar.hasClass('is-active') ) {
				elPar.removeClass('is-active');
				elTar.slideUp(400);
			} else {
				elPar.addClass('is-active');
				elTar.slideDown(400);
			}
		});

	}

}

module.exports = Header;
