'use strict';

const $ = require('jquery');
const App = require('./modules/app.js');
const ScrollTo = require('./modules/scrollTo.js');
const Sections = require('./modules/sections.js');
const Accordion = require('./modules/accordion.js');
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
