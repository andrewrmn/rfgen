// JavaScript Document
// Passive event listeners
jQuery.event.special.touchstart = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
    }
};
jQuery.event.special.touchmove = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
    }
};

jQuery(".main-nav .menu > li > a, .main-nav ul li.dropmenu, .main-nav > ul > li ul.mega-menu").hover(function(){
jQuery(".menu-overlay").toggleClass("active");
});

jQuery(document).ready(function() {
	var offset = 220;
	var duration = 500;
	jQuery(window).scroll(function() {
	if (jQuery(this).scrollTop() > offset) {
	jQuery('.back-to-top').fadeIn(duration);
	} else {
	jQuery('.back-to-top').fadeOut(duration);
	}
	});
	jQuery('.back-to-top').click(function(event) {
	event.preventDefault();
	jQuery('html, body').animate({scrollTop: 0}, duration);
	return false;
	});
	
	jQuery('.carousel-inner').each(function () {
	if (jQuery(this).children('div').length === 1)
	jQuery(this).siblings('.carousel-control-prev, .carousel-control-next, .carousel-indicators').hide();
	});

	jQuery('.CarouselOwl').owlCarousel({
		margin: 5,
		nav: true,
		loop: false,
		responsive: {
		0: {items: 4},
		480: {items: 1},
		576: {items: 5},
		768: {items: 10},
		992: {items: 13},
		1200: {items: 13}
		}
	});
	jQuery('.CarouselOwl-1').owlCarousel({
		margin: 5,
		nav: true,
		loop: true,
		responsive: {
		0: {items: 1},
		480: {items: 1},
		576: {items: 1},
		768: {items: 1},
		992: {items: 1},
		1200: {items: 1}
		}
	});
	jQuery('.CarouselOwl-full').owlCarousel({
		margin: 5,
		nav: false,
		loop: true,
		responsive: {
		0: {items: 1},
		480: {items: 1},
		576: {items: 1},
		768: {items: 1},
		992: {items: 1},
		1200: {items: 1}
		}
	});
	jQuery('.CarouselOwl-More').owlCarousel({
		margin: 0,
		nav: true,
		loop: true,
		responsive: {
		0: {items: 1},
		480: {items: 1},
		576: {items: 1},
		768: {items: 2},
		992: {items: 3},
		1200: {items: 3}
		}
	});
	jQuery('.CarouselOwlTeam').owlCarousel({
		margin: 0,
		nav: false,
		loop: false,
		responsive: {
		0: {items: 1},
		480: {items: 1},
		576: {items: 2},
		768: {items: 3},
		992: {items: 4},
		1200: {items: 4}
		}
	});
	jQuery('.CarouselOwlLogo').owlCarousel({
		margin: 30,
		nav: false,
		loop: true,
		responsive: {
		0: {items: 2},
		480: {items: 2},
		576: {items: 2},
		768: {items: 4},
		992: {items: 4},
		1200: {items: 4}
		}
	});
	jQuery('.TrustedLogo').owlCarousel({
		margin: 40,
		nav: false,
		loop: false,
		responsive: {
		0: {items: 3},
		480: {items: 3},
		576: {items: 3},
		768: {items: 4},
		992: {items: 6},
		1200: {items: 6}
		}
	});
	jQuery('.SuccessStroy').owlCarousel({
		margin: 48,
		nav: false,
		loop: false,
		responsive: {
		0: {items: 1},
		480: {items: 1},
		576: {items: 1},
		768: {items: 2},
		992: {items: 3},
		1200: {items: 3}
		}
	});
});



$(function() {
$('a.page-scroll').bind('click', function(event) {
var $anchor = $(this);
$('html, body').stop().animate({
scrollTop: $($anchor.attr('href')).offset().top
}, 1500, 'easeInOutExpo');
event.preventDefault();
});
});

jQuery(function () {
jQuery(".img-crop").responsiveImageCropper();
});

!function (e) {
var t = function () {};
t.prototype = {targetElements: void 0, options: void 0, run: function (e) {
var t = this;
this.targetElements = new Array, e.each(function (e) {
var i = jQuery(this);
i.css({display: "none"});
var a = new Image;
a.onload = function () {
i.css({position: "absolute"}), t.targetElements.push(i), t.croppingImageElement(i), i.css({display: "block"})
}, a.src = i.attr("src")
}), jQuery(window).resize(function (e) {
t.onResizeCallback()
})
}, onResizeCallback: function () {
var t = this;
e.each(this.targetElements, function (e) {
var i = this;
t.croppingImageElement(i)
})
}, croppingImageElement: function (t) {
var i, a;
t.data("crop-image-wrapped") ? (a = t.data("crop-image-outer"), i = t.data("crop-image-inner")) : (a = e("<div>"), i = e("<div>"), a.css({overflow: "hidden", margin: t.css("margin"), padding: t.css("padding")}), t.css({margin: 0, padding: 0}), i.css({position: "relative", overflow: "hidden"}), t.after(a), a.append(i), i.append(t), t.data("crop-image-outer", a), t.data("crop-image-inner", i), t.data("crop-image-wrapped", !0)), this.desideImageSizes(t)
}, desideImageSizes: function (e) {
var t = e.data("crop-image-outer"), i = e.data("crop-image-inner"), a = e.data("crop-image-ratio");
a || (a = 1);
var n = t.width() * a;
i.height(n), e.width(t.width()), e.height("auto"), e.css({position: "absolute", left: 0, top: -(e.height() - t.height()) / 2}), n > e.height() && (e.width("auto"), e.height(n), e.css({position: "absolute", left: -(e.width() - t.width()) / 2, top: 0}))
}, setOptions: function (e) {
this.options = e
}}, e.fn.responsiveImageCropper = function (i) {
var i = e.extend(e.fn.responsiveImageCropper.defaults, i), a = e(this);
return cropper = new t, cropper.setOptions(i), cropper.run(a), this
}, e.fn.responsiveImageCropper.defaults = {}
}(jQuery);