'use strict';

const $ = require('jquery');

class App {

	constructor( options ) {

		$(function(){
			setTimeout(function(){
				$('body').add('page-ready');
			}, 400);
		});

		if ( ! ('ontouchstart' in window) ) {
			document.documentElement.classList.add('no-touch');
		}

		if ( 'ontouchstart' in window ) {
			document.documentElement.classList.add('is-touch');
		}

		if (document.documentMode || /Edge/.test(navigator.userAgent)) {
			if(navigator.appVersion.indexOf('Trident') === -1) {
				document.documentElement.classList.add('isEDGE');
			} else {
				$('html').addClass('isIE isIE11');
			}
		}

		var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

		if(isSafari){
			document.body.classList.add('browser-safari');
		}

		if(window.location.hostname == 'localhost' | window.location.hostname == '127.0.0.1'){
			document.body.classList.add('localhost');
		}

		if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
			document.body.classList.add('darkmode');
			//document.getElementById('favicon').setAttribute('href', '/wp-content/themes/rfgen-software/build/images/favicons/favicon-light.png');
		}



		var scrollWrap = $('.scroll-container__inner');
		const scrollCont = $('.scroll-container');

		function overScrollLengths(){
			if(scrollCont.length) {
				let count = scrollWrap.children('.scroll-container__item').length;
				scrollWrap.each(function(){
					let el = $(this);
					let w = 328 * count;
					w = w + 'px';
					el.css("min-width", w);
					el.css("max-width", w);
					el.css("width", w);
				});

				scrollCont.each(function(){
					let el = $(this);
					let offset = el.offset();
					let offsetLeft = offset.left;
					let ww = $( window ).width();
					let w = ww - offsetLeft;
					el.css("width", w);
					console.log(ww - offsetLeft);
				});
			}
		}

		$(document).ready(function(){
			overScrollLengths();
		});

		$(window).resize(function(){
			overScrollLengths();
		});

	}

}

module.exports = App;
