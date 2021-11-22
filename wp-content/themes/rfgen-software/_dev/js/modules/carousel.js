'use strict';

const $ = require('jquery');
const slick = require('./slick');

class Carousel {

	constructor( options ) {
		//setup any defaults
		this.defaults = {};
		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );
		this.setup();
	}

	setup() {
		if( $('.js-carousel').length ) {
			this.events();
		} else {
			return;
		}
	}

	events() {

		const arrowPrev = '<span class="slick-prev"><svg viewBox="0 0 39 39"><circle fill-opacity="0" cx="19.5" cy="19.5" r="19.5"/><circle fill="#15151D" cx="19.5" cy="19.5" r="19.5"/><g><ellipse fill="#FFFFFF" cx="22.5" cy="19.5" rx="1" ry="1"/><path fill="none" stroke="#FFFFFF" d="M19.5,23.6l-4-4.1l4-4.1"/></g></svg></span>';
		const arrowNext = '<span class="slick-next"><svg viewBox="0 0 39 39"><circle fill-opacity="0" cx="19.5" cy="19.5" r="19.5"/><circle fill="#15151D" cx="19.5" cy="19.5" r="19.5"/><g><ellipse fill="#FFFFFF" cx="16.5" cy="19.5" rx="1" ry="1"/><path fill="none" stroke="#FFFFFF" d="M19.5,15.4l4,4.1l-4,4.1"/></g></svg></span>';

		const fades = $('.js-carousel-fade');
		if(fades.length){
			fades.each(function(){
				let el = $(this);
				el.slick({
					dots: false,
					arrows: true,
					infinite: true,
					speed: 400,
					slidesToShow: 1,
					autoplay: true,
					autoplaySpeed: 5000,
					fade: true,
					focusOnSelect: false,
					prevArrow: arrowPrev,
					nextArrow: arrowNext
				});
			});
		}

		let standard = $('.js-carousel-standard');
		if(standard.length){
			standard.each(function(){
				let el = $(this);
				let ds = parseInt( el.attr('data-slides') );
				let match = '#' + el.attr('data-sync');
				el.slick({
					dots: false,
					arrows: true,
					infinite: true,
					speed: 400,
					slidesToShow: ds,
			    slidesToScroll: ds,
					autoplay: true,
					autoplaySpeed: 2000,
					//fade: true,
					focusOnSelect: false,
					prevArrow: arrowPrev,
					nextArrow: arrowNext,
					asNavFor: match,
					responsive: [
					 {
						 breakpoint: 800,
						 settings: {
							 slidesToShow: 1,
							 slidesToScroll: 1,
							 arrows: false,
							 dots: false
						 }
					 }
					]
				});
			});
		}

		let sync = $('.carousel-sync');
		if(sync.length){
			sync.each(function(){
				let el = $(this);
				let match = '.' + el.attr('id');
				el.slick({
					  slidesToShow: 1,
					  slidesToScroll: 1,
					  asNavFor: match,
					  dots: true,
						arrows: false,
						fade: true
					});
			});
		}



		let slider = $('.js-slider');
		if(slider.length){
			slider.slick({
				dots: false,
				infinite: false,
				arrows: true,
				speed: 1000,
				autoplay: false,
				prevArrow: arrowPrev,
				nextArrow: arrowNext,
				slidesToShow: 3,
				slidesToScroll: 3
			});
		}



		let test = $('.js-carousel-testimonial');
		if(test.length){
			test.each(function(){
				let el = $(this);
				let match = '#' + el.attr('data-sync');
				if(match.length){
					el.slick({
						dots: false,
						arrows: false,
						infinite: true,
						speed: 400,
						slidesToShow: 1,
						slidesToScroll: 1,
						autoplay: true,
						autoplaySpeed: 8000,
						//fade: true,
						focusOnSelect: false,
						asNavFor: match,
					});
				} else {
					el.slick({
						dots: false,
						arrows: false,
						infinite: true,
						speed: 400,
						slidesToShow: 1,
						slidesToScroll: 1,
						autoplay: true,
						autoplaySpeed: 8000,
						//fade: true,
						focusOnSelect: false,
					});
				}
			});
		}

		// $('.js-carousel-feature').on("beforeChange", function (){
		// 	var leaving = $('.js-carousel-feature .slick-slide.slick-current');
		//     leaving.addClass('is-leaving');
		//
		// 	setTimeout(function(){
		// 		leaving.removeClass('is-leaving');
		// 	}, 2000);
		// });
	}

}

module.exports = Carousel;
