/**
 * NetPlus Circuit — theme interactions (vanilla JS, no jQuery dependency).
 */
(function () {
	'use strict';

	var doc = document;

	function ready(fn) {
		if (doc.readyState !== 'loading') { fn(); }
		else { doc.addEventListener('DOMContentLoaded', fn); }
	}

	/* ---------------------------------------------------------------- toasts */
	function toast(message, type) {
		var wrap = doc.getElementById('np-toast-wrap');
		if (!wrap) { return; }
		var el = doc.createElement('div');
		el.className = 'np-toast' + (type ? ' np-toast--' + type : '');
		el.setAttribute('role', 'status');
		el.textContent = message;
		wrap.appendChild(el);
		setTimeout(function () {
			el.style.opacity = '0';
			el.style.transform = 'translateX(30px)';
			el.style.transition = 'all .3s ease';
			setTimeout(function () { el.remove(); }, 320);
		}, 3800);
	}

	ready(function () {

		/* ---------------------------------------------------- mobile drawer */
		var drawer = doc.getElementById('np-drawer');
		var burger = doc.getElementById('np-burger');

		function openDrawer() {
			if (!drawer) { return; }
			drawer.classList.add('is-open');
			drawer.setAttribute('aria-hidden', 'false');
			doc.body.style.overflow = 'hidden';
			if (burger) { burger.setAttribute('aria-expanded', 'true'); }
		}
		function closeDrawer() {
			if (!drawer) { return; }
			drawer.classList.remove('is-open');
			drawer.setAttribute('aria-hidden', 'true');
			doc.body.style.overflow = '';
			if (burger) { burger.setAttribute('aria-expanded', 'false'); }
		}
		if (burger) { burger.addEventListener('click', openDrawer); }
		doc.querySelectorAll('[data-np-drawer-close]').forEach(function (el) {
			el.addEventListener('click', closeDrawer);
		});
		doc.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { closeDrawer(); closeDepts(); }
		});

		/* mobile sub-menu carets */
		doc.querySelectorAll('.np-mnav li').forEach(function (li) {
			var sub = li.querySelector('ul');
			var link = li.querySelector('a');
			if (!sub || !link) { return; }
			var caret = doc.createElement('button');
			caret.className = 'np-mnav__caret';
			caret.setAttribute('aria-label', 'Toggle submenu');
			caret.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>';
			link.parentNode.appendChild(caret);
			caret.addEventListener('click', function (e) {
				e.preventDefault();
				li.classList.toggle('is-open');
			});
		});

		/* ---------------------------------------------- departments dropdown */
		var depts = doc.getElementById('np-depts');
		function closeDepts() {
			if (depts) {
				depts.classList.remove('is-open');
				var t = depts.querySelector('.np-depts__toggle');
				if (t) { t.setAttribute('aria-expanded', 'false'); }
			}
		}
		if (depts) {
			var toggle = depts.querySelector('.np-depts__toggle');
			toggle.addEventListener('click', function (e) {
				e.stopPropagation();
				var open = depts.classList.toggle('is-open');
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			doc.addEventListener('click', function (e) {
				if (!depts.contains(e.target)) { closeDepts(); }
			});
			// Auto-open once on the homepage for discoverability (desktop only).
			if (doc.body.classList.contains('home') && window.innerWidth > 992) {
				setTimeout(function () {
					if (!sessionStorage.getItem('npDeptsShown')) {
						depts.classList.add('is-open');
						toggle.setAttribute('aria-expanded', 'true');
						sessionStorage.setItem('npDeptsShown', '1');
						setTimeout(closeDepts, 3200);
					}
				}, 900);
			}
		}

		/* ------------------------------------------- account dropdown (touch) */
		doc.querySelectorAll('.np-acc-dd').forEach(function (dd) {
			var btn = dd.querySelector('.np-haction');
			btn.addEventListener('click', function (e) {
				if (window.innerWidth <= 992) {
					e.preventDefault();
					dd.classList.toggle('is-open');
				}
			});
		});

		/* ------------------------------------------------------ sticky navbar */
		var navbar = doc.getElementById('np-navbar');
		if (navbar && doc.body.classList.contains('np-sticky-enabled')) {
			var spacer = doc.getElementById('np-nav-spacer');
			var sentinelTop = navbar.offsetTop;
			function onScrollNav() {
				if (window.scrollY > sentinelTop + 40) {
					navbar.classList.add('is-stuck');
					doc.body.classList.add('np-has-stuck');
					if (spacer) { spacer.style.height = navbar.offsetHeight + 'px'; }
				} else {
					navbar.classList.remove('is-stuck');
					doc.body.classList.remove('np-has-stuck');
				}
			}
			window.addEventListener('scroll', onScrollNav, { passive: true });
			onScrollNav();
		}

		/* ------------------------------------------------------- hero slider */
		var hero = doc.getElementById('np-hero');
		if (hero) {
			var track = doc.getElementById('np-hero-track');
			var slides = track ? track.children.length : 0;
			var dots = doc.querySelectorAll('.np-hero__dot');
			var current = 0;
			var timer = null;
			var delay = parseInt(hero.getAttribute('data-autoplay') || '6000', 10);

			function goTo(i) {
				if (slides < 2) { return; }
				current = (i + slides) % slides;
				track.style.transform = 'translateX(-' + (current * 100) + '%)';
				dots.forEach(function (d, di) { d.classList.toggle('is-active', di === current); });
			}
			function play() {
				if (slides < 2) { return; }
				stop();
				timer = setInterval(function () { goTo(current + 1); }, delay);
			}
			function stop() { if (timer) { clearInterval(timer); timer = null; } }

			var prev = doc.getElementById('np-hero-prev');
			var next = doc.getElementById('np-hero-next');
			if (prev) { prev.addEventListener('click', function () { goTo(current - 1); play(); }); }
			if (next) { next.addEventListener('click', function () { goTo(current + 1); play(); }); }
			dots.forEach(function (d) {
				d.addEventListener('click', function () { goTo(parseInt(d.getAttribute('data-slide'), 10)); play(); });
			});
			hero.addEventListener('mouseenter', stop);
			hero.addEventListener('mouseleave', play);

			/* touch swipe */
			var x0 = null;
			hero.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; stop(); }, { passive: true });
			hero.addEventListener('touchend', function (e) {
				if (x0 === null) { return; }
				var dx = e.changedTouches[0].clientX - x0;
				if (Math.abs(dx) > 40) { goTo(dx < 0 ? current + 1 : current - 1); }
				x0 = null; play();
			}, { passive: true });

			play();
		}

		/* ------------------------------------------------------ product tabs */
		doc.querySelectorAll('.np-ptab').forEach(function (tab) {
			tab.addEventListener('click', function () {
				var wrap = tab.closest('section') || doc;
				wrap.querySelectorAll('.np-ptab').forEach(function (t) {
					t.classList.remove('is-active');
					t.setAttribute('aria-selected', 'false');
				});
				tab.classList.add('is-active');
				tab.setAttribute('aria-selected', 'true');
				doc.querySelectorAll('.np-ppanel').forEach(function (p) { p.classList.remove('is-active'); });
				var panel = doc.getElementById(tab.getAttribute('data-target'));
				if (panel) { panel.classList.add('is-active'); }
			});
		});

		/* -------------------------------------------------------- countdowns */
		doc.querySelectorAll('[data-countdown]').forEach(function (box) {
			var end = new Date(box.getAttribute('data-countdown')).getTime();
			var dEl = box.querySelector('[data-cd="d"]');
			var hEl = box.querySelector('[data-cd="h"]');
			var mEl = box.querySelector('[data-cd="m"]');
			var sEl = box.querySelector('[data-cd="s"]');
			function pad(n) { return n < 10 ? '0' + n : '' + n; }
			function setNum(el, val) {
				if (!el || el.textContent === val) { return; }
				el.textContent = val;
				el.classList.remove('tick');
				void el.offsetWidth;
				el.classList.add('tick');
			}
			function tick() {
				var diff = end - Date.now();
				if (isNaN(diff)) { return; }
				if (diff <= 0) {
					dEl.textContent = hEl.textContent = mEl.textContent = sEl.textContent = '00';
					return;
				}
				var s = Math.floor(diff / 1000);
				setNum(dEl, pad(Math.floor(s / 86400)));
				setNum(hEl, pad(Math.floor((s % 86400) / 3600)));
				setNum(mEl, pad(Math.floor((s % 3600) / 60)));
				setNum(sEl, pad(s % 60));
			}
			tick();
			setInterval(tick, 1000);
		});

		/* ------------------------------------------------ product carousels */
		doc.querySelectorAll('.np-carousel').forEach(function (car) {
			var track = car.querySelector('.np-carousel__track');
			var prev = car.querySelector('[data-car="prev"]');
			var next = car.querySelector('[data-car="next"]');
			if (!track) { return; }
			function step() {
				var card = track.querySelector('.np-product-card');
				var w = card ? card.getBoundingClientRect().width + 16 : 260;
				return Math.max(1, Math.floor(track.clientWidth / w)) * w;
			}
			if (prev) { prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); }); }
			if (next) { next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); }); }
			/* touch swipe works natively via scroll-snap */
		});

		/* ------------------------------------------------------ FAQ accordion */
		doc.querySelectorAll('.np-faq__q').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var item = btn.closest('.np-faq__item');
				var answer = item.querySelector('.np-faq__a');
				var open = item.classList.toggle('is-open');
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				answer.style.maxHeight = open ? (answer.scrollHeight + 'px') : '0px';
			});
		});

		/* ------------------------------------------- scroll-reveal animations */
		if (doc.body.classList.contains('np-anim') && 'IntersectionObserver' in window) {
			var revealTargets = doc.querySelectorAll(
				'.np-sec-head, .np-cats, .np-promos, .np-trust__grid, .np-carousel, .np-products, .np-posts, .np-newsletter, .np-wa-strip, .np-faq, .np-deal, .np-flash-head, .np-promo-strip'
			);
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (en) {
					if (en.isIntersecting) {
						en.target.classList.add('is-in');
						io.unobserve(en.target);
					}
				});
			}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
			revealTargets.forEach(function (el, i) {
				el.classList.add('np-reveal');
				el.style.transitionDelay = Math.min(i % 6, 4) * 70 + 'ms';
				io.observe(el);
			});
		}

		/* ------------------------------------------------------- back to top */
		var toTop = doc.getElementById('np-totop');
		if (toTop) {
			window.addEventListener('scroll', function () {
				toTop.classList.toggle('is-visible', window.scrollY > 500);
			}, { passive: true });
			toTop.addEventListener('click', function () {
				window.scrollTo({ top: 0, behavior: 'smooth' });
			});
		}

		/* ---------------------------------------------------- WhatsApp bubble */
		var bubble = doc.getElementById('np-wa-bubble');
		if (bubble && !sessionStorage.getItem('npWaBubbleClosed')) {
			var bDelay = parseInt(bubble.getAttribute('data-delay') || '5', 10) * 1000;
			setTimeout(function () { bubble.classList.add('is-visible'); }, bDelay);
			var bClose = doc.getElementById('np-wa-bubble-close');
			if (bClose) {
				bClose.addEventListener('click', function (e) {
					e.preventDefault(); e.stopPropagation();
					bubble.classList.remove('is-visible');
					sessionStorage.setItem('npWaBubbleClosed', '1');
				});
			}
			var waBtn = doc.getElementById('np-wa-float');
			if (waBtn) {
				waBtn.addEventListener('click', function () {
					sessionStorage.setItem('npWaBubbleClosed', '1');
				});
			}
		}

		/* --------------------------------------------------------- cookie bar */
		var cookie = doc.getElementById('np-cookie');
		if (cookie && !localStorage.getItem('npCookieChoice')) {
			setTimeout(function () { cookie.classList.add('is-visible'); }, 1600);
			var accept = doc.getElementById('np-cookie-accept');
			var decline = doc.getElementById('np-cookie-decline');
			if (accept) { accept.addEventListener('click', function () { localStorage.setItem('npCookieChoice', 'accepted'); cookie.classList.remove('is-visible'); }); }
			if (decline) { decline.addEventListener('click', function () { localStorage.setItem('npCookieChoice', 'declined'); cookie.classList.remove('is-visible'); }); }
		}

		/* ------------------------------------------------ quantity +/- buttons */
		function enhanceQty(container) {
			(container || doc).querySelectorAll('.quantity:not(.np-qty-done)').forEach(function (q) {
				q.classList.add('np-qty', 'np-qty-done');
				var input = q.querySelector('input.qty');
				if (!input) { return; }
				var minus = doc.createElement('button');
				minus.type = 'button'; minus.textContent = '−'; minus.setAttribute('aria-label', 'Decrease quantity');
				var plus = doc.createElement('button');
				plus.type = 'button'; plus.textContent = '+'; plus.setAttribute('aria-label', 'Increase quantity');
				q.insertBefore(minus, input);
				q.appendChild(plus);
				minus.addEventListener('click', function () {
					var v = parseInt(input.value || '1', 10);
					var min = parseInt(input.getAttribute('min') || '0', 10);
					if (v > (isNaN(min) ? 0 : min)) { input.value = v - 1; input.dispatchEvent(new Event('change', { bubbles: true })); }
				});
				plus.addEventListener('click', function () {
					var v = parseInt(input.value || '1', 10);
					var max = parseInt(input.getAttribute('max') || '0', 10);
					if (!max || max === 'NaN' || v < max) { input.value = v + 1; input.dispatchEvent(new Event('change', { bubbles: true })); }
				});
			});
		}
		enhanceQty(doc);

		/* ---------------------------------------------- AJAX forms (theme) */
		doc.querySelectorAll('form.np-ajax-form').forEach(function (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var kind = form.getAttribute('data-np-form');
				var statusEl = form.querySelector('.np-form-status');
				var submitBtn = form.querySelector('button[type="submit"]');
				var data = new FormData(form);
				var action = kind === 'newsletter' ? 'np_newsletter_subscribe' : 'np_contact_form';
				data.set('action', action);
				if (kind !== 'newsletter') { data.set('action', action); }

				if (submitBtn) { submitBtn.disabled = true; submitBtn.dataset.old = submitBtn.innerHTML; submitBtn.innerHTML = (window.npCircuit && npCircuit.i18n ? npCircuit.i18n.sending : 'Sending…'); }

				fetch((window.npCircuit ? npCircuit.ajaxUrl : '/wp-admin/admin-ajax.php'), {
					method: 'POST',
					credentials: 'same-origin',
					body: data
				})
					.then(function (r) { return r.json(); })
					.then(function (res) {
						var ok = res && res.success;
						var msg = (res && res.data && res.data.message) ? res.data.message : (ok ? 'Done!' : 'Error.');
						if (statusEl) {
							statusEl.hidden = false;
							statusEl.className = 'np-form-status ' + (ok ? 'np-form-status--ok' : 'np-form-status--err');
							statusEl.textContent = msg;
						}
						if (ok) {
							if (kind === 'newsletter') { form.querySelector('input[type="email"]').value = ''; }
							else { form.querySelectorAll('input:not([type=hidden]), textarea').forEach(function (f) { f.value = ''; }); }
						}
					})
					.catch(function () {
						if (statusEl) {
							statusEl.hidden = false;
							statusEl.className = 'np-form-status np-form-status--err';
							statusEl.textContent = (window.npCircuit && npCircuit.i18n ? npCircuit.i18n.error : 'Something went wrong.');
						}
					})
					.finally(function () {
						if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = submitBtn.dataset.old || submitBtn.innerHTML; }
					});
			});
		});

		/* ------------------------------------------------- Woo AJAX toasts */
		if (typeof jQuery !== 'undefined') {
			jQuery(doc.body).on('added_to_cart', function (e, fragments, hash, button) {
				toast((window.npCircuit && npCircuit.i18n ? npCircuit.i18n.addedToCart : 'Added to cart') + ' ✓');
			}).on('error', function () {
				toast((window.npCircuit && npCircuit.i18n ? npCircuit.i18n.error : 'Something went wrong.'), 'error');
			}).on('updated_cart_totals', function () {
				enhanceQty(doc);
			}).on('updated_checkout', function () {
				enhanceQty(doc);
			});
		}

		/* -------------------------------------- non-JS form result messages */
		var params = new URLSearchParams(window.location.search);
		if (params.get('np_form')) {
			var okFlag = params.get('np_form') === 'sent';
			toast(decodeURIComponent(params.get('np_msg') || (okFlag ? 'Sent!' : 'Error')), okFlag ? '' : 'error');
			history.replaceState(null, '', window.location.pathname);
		}
	});

	/* Re-run enhancements after Woo fragment updates */
	if (typeof jQuery !== 'undefined') {
		jQuery(document.body).on('wc_fragments_loaded', function () { });
	}
})();
