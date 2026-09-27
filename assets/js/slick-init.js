jQuery(function ($) {
	'use strict';

	function initSliders() {
		$('.mansa-slick').each(function () {
			var $slider = $(this);

			if ($slider.hasClass('slick-initialized')) {
				return;
			}

			var slideCount = $slider.children().length;

			if (slideCount === 0) {
				return;
			}

			// Single slide fallback: no infinite loop or carousel needed
			if (slideCount === 1) {
				$slider.slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					arrows: false,
					infinite: false,
					centerMode: false,
					adaptiveHeight: true
				});
				return;
			}

			$slider.addClass('mansa-slick--center');

			$slider.slick({
				mobileFirst: false,
				respondTo: 'window',
				slidesToShow: Math.min(3, slideCount),
				slidesToScroll: 1,
				centerMode: true,
				centerPadding: '50px',
				dots: true,
				arrows: true,
				infinite: true,
				focusOnSelect: true,
				swipeToSlide: true,
				speed: 400,
				cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)',
				responsive: [
					{
						breakpoint: 1025,
						settings: {
							slidesToShow: Math.min(3, slideCount),
							slidesToScroll: 1,
							centerMode: true,
							centerPadding: '40px',
							arrows: true,
							dots: true,
							infinite: true
						}
					},
					{
						breakpoint: 769,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							centerMode: false,
							centerPadding: '0px',
							arrows: false,
							dots: true,
							infinite: true
						}
					},
					{
						breakpoint: 481,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							centerMode: false,
							centerPadding: '0px',
							arrows: false,
							dots: true,
							infinite: true
						}
					}
				]
			});

			// Recalculate track position when card images finish loading
			$slider.find('img').on('load', function () {
				$slider.slick('setPosition');
			});
		});
	}

	initSliders();

	// Force resize recalculation when switching between desktop and mobile in DevTools
	var resizeTimer;
	$(window).on('resize orientationchange', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			$('.mansa-slick.slick-initialized').slick('resize');
		}, 100);
	});
});


