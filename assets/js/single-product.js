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

	initProductGallerySlider();

	var resizeTimer;
	$(window).on('resize orientationchange', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			$('.mansa-product-gallery__slider.slick-initialized').slick('resize');
		}, 100);
	});
});
