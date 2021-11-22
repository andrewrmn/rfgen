'use strict';

const $ = require('jquery');

class Tabs {

	constructor( options ) {

		//setup any defaults
		this.defaults = {};

		//merge options with defaults
		this.settings = $.extend( true, {}, this.defaults, options );

		if( $('form').length ) {
			this.events();
		} else {
			return;
		}
	}

	events() {

    $('.dynamic-labels input, .dynamic-labels select').on('focus', function() {
      $(this).parents('.field-wrap').addClass('is-active');
    });

    $('.dynamic-labels input, .dynamic-labels select').on('focusout', function() {
      if( $(this).val().length === 0 ) {
        $(this).parents('.field-wrap').removeClass('is-active');
      } else {
        $(this).parents('.field-wrap').removeClass('error');
      }
    });


		$('.gform_wrapper input, .gform_wrapper select').on('focus', function() {
			$(this).parents('.gfield').addClass('is-active');
		});

		$('.gform_wrapper input, .gform_wrapper select').on('focusout', function() {
			if( $(this).val().length === 0 ) {
				$(this).parents('.gfield').removeClass('is-active');
			} else {
				$(this).parents('.gfield').removeClass('error');
			}
		});

    $(document).ready(function(){

			$(".gfield:has(.ginput_container_textarea)").addClass('gfield_textarea');

      $('.dynamic-labels input, .dynamic-labels select').each(function() {
        if( $(this).val().length !== 0 ) {
          $(this).parents('.field-wrap').addClass('is-active');
        }
      });

			$('.gform_wrapper input, .gform_wrapper select').each(function() {
        if( $(this).val().length !== 0 ) {
          $(this).parents('.gfield').addClass('is-active');
        }
      });
    });

		$(document).on('gform_post_render', function(){
			$(".gfield:has(.ginput_container_textarea)").addClass('gfield_textarea');
      $('.dynamic-labels input, .dynamic-labels select').each(function() {
        if( $(this).val().length !== 0 ) {
          $(this).parents('.field-wrap').addClass('is-active');
        }
      });
			$('.gform_wrapper input, .gform_wrapper select').each(function() {
        if( $(this).val().length !== 0 ) {
          $(this).parents('.gfield').addClass('is-active');
        }
      });
    });

	}

}

module.exports = Tabs;
