/**
 * Like and Share functionality for Mansa Products & Shortcode instances
 */
(function () {
	'use strict';

	function initLikeShare() {
		var likeBtns = document.querySelectorAll('.mansa-support-btn--like');
		var shareBtns = document.querySelectorAll('.mansa-support-btn--share');

		if (!likeBtns.length && !shareBtns.length) {
			return;
		}

		var config = window.mansa_like_share || {};
		var ajaxUrl = config.ajax_url || '/wp-admin/admin-ajax.php';
		var nonce = config.nonce || '';
		var copiedMsg = config.copied_text || 'Link copied to clipboard!';

		/* ----------------------------------------------------
		 * Toast Notification Helper
		 * ---------------------------------------------------- */
		function showToast(message) {
			var existing = document.querySelector('.mansa-toast');
			if (existing) {
				existing.remove();
			}

			var toast = document.createElement('div');
			toast.className = 'mansa-toast';
			toast.textContent = message;
			document.body.appendChild(toast);

			requestAnimationFrame(function () {
				toast.classList.add('is-visible');
			});

			setTimeout(function () {
				toast.classList.remove('is-visible');
				setTimeout(function () {
					toast.remove();
				}, 300);
			}, 2500);
		}

		/* ----------------------------------------------------
		 * Like Functionality (Supports multiple buttons)
		 * ---------------------------------------------------- */
		likeBtns.forEach(function (btn) {
			if (btn.dataset.initialized) {
				return;
			}
			btn.dataset.initialized = 'true';

			var postId = btn.getAttribute('data-product-id');
			var storageKey = 'mansa_product_liked_' + postId;
			var countEl = btn.querySelector('.mansa-support-btn__count');
			var isProcessing = false;

			if (localStorage.getItem(storageKey) === '1') {
				btn.classList.add('is-liked');
			}

			btn.addEventListener('click', function (e) {
				e.preventDefault();
				if (isProcessing) {
					return;
				}

				var isLiked = btn.classList.contains('is-liked');
				var actionType = isLiked ? 'unlike' : 'like';
				var currentCount = parseInt(countEl ? countEl.textContent : '0', 10) || 0;

				// Optimistic UI update across all buttons matching this postId
				var matchingBtns = document.querySelectorAll('.mansa-support-btn--like[data-product-id="' + postId + '"]');

				if (actionType === 'like') {
					currentCount++;
					matchingBtns.forEach(function (b) {
						b.classList.add('is-liked');
						var c = b.querySelector('.mansa-support-btn__count');
						if (c) {
							c.textContent = currentCount;
							c.classList.add('has-count');
						}
					});
					localStorage.setItem(storageKey, '1');
				} else {
					currentCount = Math.max(0, currentCount - 1);
					matchingBtns.forEach(function (b) {
						b.classList.remove('is-liked');
						var c = b.querySelector('.mansa-support-btn__count');
						if (c) {
							c.textContent = currentCount > 0 ? currentCount : '';
							if (currentCount === 0) {
								c.classList.remove('has-count');
							}
						}
					});
					localStorage.removeItem(storageKey);
				}

				isProcessing = true;

				var formData = new FormData();
				formData.append('action', 'mansa_toggle_like');
				formData.append('product_id', postId);
				formData.append('like_action', actionType);
				formData.append('nonce', nonce);

				fetch(ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				})
					.then(function (res) {
						return res.json();
					})
					.then(function (data) {
						if (data && data.success && data.data) {
							var count = data.data.likes;
							matchingBtns.forEach(function (b) {
								var c = b.querySelector('.mansa-support-btn__count');
								if (c) {
									c.textContent = count > 0 ? count : '';
									if (count > 0) {
										c.classList.add('has-count');
									} else {
										c.classList.remove('has-count');
									}
								}
							});
						}
					})
					.catch(function () {
						// On error, keep optimistic count
					})
					.finally(function () {
						isProcessing = false;
					});
			});
		});

		/* ----------------------------------------------------
		 * Share Functionality
		 * ---------------------------------------------------- */
		shareBtns.forEach(function (btn) {
			if (btn.dataset.initialized) {
				return;
			}
			btn.dataset.initialized = 'true';

			btn.addEventListener('click', function (e) {
				e.preventDefault();

				var shareTitle = btn.getAttribute('data-title') || document.title;
				var shareUrl = btn.getAttribute('data-url') || window.location.href;

				if (navigator.share) {
					navigator.share({
						title: shareTitle,
						text: shareTitle,
						url: shareUrl,
					}).catch(function (err) {
						if (err.name !== 'AbortError') {
							copyToClipboard(shareUrl);
						}
					});
				} else {
					copyToClipboard(shareUrl);
				}
			});
		});

		function copyToClipboard(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(function () {
					showToast(copiedMsg);
				}).catch(function () {
					fallbackCopy(text);
				});
			} else {
				fallbackCopy(text);
			}
		}

		function fallbackCopy(text) {
			var textarea = document.createElement('textarea');
			textarea.value = text;
			textarea.style.position = 'fixed';
			textarea.style.opacity = '0';
			document.body.appendChild(textarea);
			textarea.focus();
			textarea.select();
			try {
				document.execCommand('copy');
				showToast(copiedMsg);
			} catch (err) {
				showToast(text);
			}
			document.body.removeChild(textarea);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initLikeShare);
	} else {
		initLikeShare();
	}
})();
