(function (window, document) {
	'use strict';

	function initCarousel(containerSelector) {
		const containers = document.querySelectorAll(containerSelector);

		containers.forEach(function (container) {
			const track = container.querySelector('.carousel__track');
			const slides = Array.from(track.children);
			const nextButton = container.querySelector('.carousel__button--next');
			const prevButton = container.querySelector('.carousel__button--prev');
			const slideWidth = slides[0].getBoundingClientRect().width;
			const amountToMove = slideWidth + 24; // 24px gap

			let currentIndex = 0;

			function setSlidePosition(slide, index) {
				slide.style.left = amountToMove * index + 'px';
			}

			function moveToSlide(newIndex) {
				const track = container.querySelector('.carousel__track');
				track.style.transform = 'translateX(-' + amountToMove * newIndex + 'px)';
				currentIndex = newIndex;

				// Update button states
				prevButton.disabled = currentIndex === 0;
				nextButton.disabled = currentIndex >= slides.length - 1;
			}

			// Initialize slide positions
			slides.forEach(setSlidePosition);
			moveToSlide(0);

			nextButton.addEventListener('click', function () {
				if (currentIndex < slides.length - 1) {
					moveToSlide(currentIndex + 1);
				}
			});

			prevButton.addEventListener('click', function () {
				if (currentIndex > 0) {
					moveToSlide(currentIndex - 1);
				}
			});
		});
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initCarousel('.carousel');
		});
	} else {
		initCarousel('.carousel');
	}
})(window, document);
