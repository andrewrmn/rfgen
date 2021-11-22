'use strict';

const $ = require('jquery');

class Tabs {

	constructor( options ) {
		
		//setup any defaults
		this.defaults = {};

		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );

		if( $('.tabs').length ) {
			this.events();
		} else {
			return;
		}
	}

	events() {

		var tabs = document.querySelectorAll('*[data-tab]'),
		tabBody = document.querySelectorAll('*[data-tab-bd]');

		for(var i = 0; i < tabs.length; i++) {
			var el = tabs[i];

			el.onclick = function() {
				var active = document.querySelector('*[data-tab].is-active'),
				target = this.getAttribute('data-tab');


				for(var t = 0; t < tabBody.length; t++) {
					if ( tabBody[t].getAttribute('data-tab-bd') == target) {
						tabBody[t].classList.add('is-active');
					} else {
						tabBody[t].classList.remove('is-active');
					}
				}
				active.classList.remove('is-active');
				this.classList.add('is-active');
			}
		}

	}

}

module.exports = Tabs;
