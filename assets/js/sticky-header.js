/**
 * Sticky Header for GeneratePress Mansa Child
 * Works on both Desktop (PC) and Mobile
 */
(function () {
	'use strict';

	function initStickyHeader() {
		var header = document.querySelector('.site-header') ||
			document.querySelector('#masthead') ||
			document.querySelector('.header') ||
			document.querySelector('#site-navigation');

		if (!header) {
			return;
		}

		// Create a spacer to prevent layout shift when header becomes fixed
		var spacer = document.querySelector('.sticky-header-spacer');
		if (!spacer) {
			spacer = document.createElement('div');
			spacer.className = 'sticky-header-spacer';
			spacer.style.display = 'none';
			header.parentNode.insertBefore(spacer, header);
		}

		var adminBar = document.getElementById('wpadminbar');

		function getAdminBarOffset() {
			if (!adminBar) {
				return 0;
			}
			var rect = adminBar.getBoundingClientRect();
			return rect.bottom > 0 ? rect.bottom : 0;
		}

		var headerInitialTop = 0;

		function getHeaderOffsetTop() {
			if (!header.classList.contains('is-sticky')) {
				var rect = header.getBoundingClientRect();
				headerInitialTop = rect.top + (window.pageYOffset || document.documentElement.scrollTop);
			}
			return headerInitialTop;
		}

		function onScroll() {
			var scrollY = window.pageYOffset || document.documentElement.scrollTop;
			var adminBarOffset = getAdminBarOffset();
			var triggerPoint = getHeaderOffsetTop() - adminBarOffset;

			if (scrollY > triggerPoint && scrollY > 0) {
				if (!header.classList.contains('is-sticky')) {
					spacer.style.height = header.offsetHeight + 'px';
					spacer.style.display = 'block';
					header.classList.add('is-sticky');
					document.body.classList.add('has-sticky-header');
				}
				header.style.top = adminBarOffset + 'px';
			} else {
				if (header.classList.contains('is-sticky')) {
					header.classList.remove('is-sticky');
					document.body.classList.remove('has-sticky-header');
					header.style.top = '';
					spacer.style.display = 'none';
					spacer.style.height = '0';
					// Re-measure after returning to normal flow
					getHeaderOffsetTop();
				}
			}
		}

		getHeaderOffsetTop();
		onScroll();

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', function () {
			if (header.classList.contains('is-sticky')) {
				spacer.style.height = header.offsetHeight + 'px';
			} else {
				getHeaderOffsetTop();
			}
			onScroll();
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initStickyHeader);
	} else {
		initStickyHeader();
	}
})();
