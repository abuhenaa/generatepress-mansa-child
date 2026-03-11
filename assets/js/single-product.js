(function () {
	'use strict';

	function initProductGallery() {
		var gallery = document.querySelector('.mansa-product-gallery');
		if (!gallery) {
			return;
		}

		var mainImage = gallery.querySelector('.mansa-product-gallery__main img');
		var thumbs = gallery.querySelectorAll('.mansa-product-gallery__thumb');

		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function (event) {
				event.preventDefault();

				if (!mainImage) {
					return;
				}

				var src = thumb.getAttribute('data-full');
				var alt = thumb.getAttribute('data-alt');

				if (src) {
					mainImage.setAttribute('src', src);
				}

				if (alt) {
					mainImage.setAttribute('alt', alt);
				}

				thumbs.forEach(function (t) {
					t.classList.remove('active');
				});
				thumb.classList.add('active');
			});
		});
	}

	function initFaqToggles() {
		var toggles = document.querySelectorAll('.mansa-faq__question');
		if (!toggles.length) {
			return;
		}

		toggles.forEach(function (toggle) {
			toggle.addEventListener('click', function () {
				var item = toggle.closest('.mansa-faq__item');
				if (!item) {
					return;
				}
				item.classList.toggle('open');
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initProductGallery();
		initFaqToggles();
	});
})();
