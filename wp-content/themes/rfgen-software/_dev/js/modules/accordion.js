'use strict';

const $ = require('jquery');

class Accordion {

	constructor( options ) {
		//setup any defaults
		this.defaults = {};
		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );
		this.setup();
	}

	setup() {

		if( $('.accordion').length ) {
			this.events();
        } else {
            return;
        }

	}

	events() {

        var acc = document.getElementsByClassName("accordion__hd");

        for (var i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.parentNode.classList.toggle("is-open");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight){
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
	}

}

module.exports = Accordion;
