/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const $ = __webpack_require__(0);
const App = __webpack_require__(2);
const ScrollTo = __webpack_require__(3);
const Sections = __webpack_require__(4);
const Accordion = __webpack_require__(5);
// const Viewport = require('./modules/viewport.js');
// const Header = require('./modules/header.js');
// const Tabs = require('./modules/tabs.js');
//
// const Carousel = require('./modules/carousel.js');
//
// //const Form = require('./modules/form.js');
//
// const Rotate = require('./modules/rotate.js');
// const Click = require('./modules/click.js');

$(function(){
	let app = new App();
	let scrollTo = new ScrollTo();
	let sections = new Sections();
	let accordion = new Accordion();
	// let viewport = new Viewport();
	// let header = new Header();
	// let tabs = new Tabs();
	// let carousel = new Carousel();
	//
	// let rotate = new Rotate();
	// let click = new Click();
});


/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const $ = __webpack_require__(0);

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


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const $ = __webpack_require__(0);

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


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const $ = __webpack_require__(0);

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


/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const $ = __webpack_require__(0);

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


/***/ })
/******/ ]);