jQuery(function ($) {
	'use strict';

	function initProductGallerySlider() {
		var $gallery = $('.mansa-product-gallery__slider');
		if (!$gallery.length) {
			return;
		}

		if ($gallery.hasClass('slick-initialized')) {
			return;
		}

		$gallery.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			dots: true,
			arrows: true,
			infinite: true,
			fade: true,
			speed: 350,
			cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)',
			adaptiveHeight: false,
			prevArrow: '<button type="button" class="slick-prev" aria-label="Previous">‹</button>',
			nextArrow: '<button type="button" class="slick-next" aria-label="Next">›</button>'
		});

		// Recalculate position when images finish loading
		$gallery.find('img').on('load', function () {
			$gallery.slick('setPosition');
		});
	}

	function initTestimonialsSlider() {
		var $testimonials = $('.mansa-product-testimonials-slider');
		if (!$testimonials.length) {
			return;
		}

		if ($testimonials.hasClass('slick-initialized')) {
			return;
		}

		var slideCount = $testimonials.children().length;

		if (slideCount <= 1) {
			return;
		}

		$testimonials.slick({
			mobileFirst: false,
			slidesToShow: Math.min(3, slideCount),
			slidesToScroll: 1,
			dots: true,
			arrows: true,
			infinite: true,
			centerMode: slideCount >= 3,
			centerPadding: slideCount >= 3 ? '30px' : '0px',
			speed: 400,
			cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)',
			adaptiveHeight: false,
			prevArrow: '<button type="button" class="slick-prev" aria-label="Previous">‹</button>',
			nextArrow: '<button type="button" class="slick-next" aria-label="Next">›</button>',
			responsive: [
				{
					breakpoint: 960,
					settings: {
						slidesToShow: Math.min(2, slideCount),
						slidesToScroll: 1,
						centerMode: false,
						centerPadding: '0px',
						arrows: true
					}
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						centerMode: false,
						centerPadding: '0px',
						arrows: false
					}
				}
			]
		});

		$testimonials.find('img').on('load', function () {
			$testimonials.slick('setPosition');
		});
	}

	initProductGallerySlider();
	initTestimonialsSlider();

	var resizeTimer;
	$(window).on('resize orientationchange', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			$('.mansa-product-gallery__slider.slick-initialized, .mansa-product-testimonials-slider.slick-initialized').slick('resize');
		}, 100);
	});
});
