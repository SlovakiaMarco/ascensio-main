/**
 * Ascensio – interakcie: hlavička, mobilné menu, filter ponúk, formuláre.
 * Bez závislostí (žiadny jQuery).
 */
(function () {
	'use strict';

	var cfg = window.ascensio || { ajaxUrl: '', i18n: {} };

	/* ---------- Hlavička: tieň po scrollovaní ---------- */
	var header = document.getElementById('asc-header');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* ---------- Mobilné menu ---------- */
	var burger = document.querySelector('.asc-burger');
	var nav = document.getElementById('asc-nav');
	if (burger && nav) {
		var setOpen = function (open) {
			document.body.classList.toggle('asc-nav-open', open);
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		};
		burger.addEventListener('click', function () {
			setOpen(!document.body.classList.contains('asc-nav-open'));
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) {
				setOpen(false);
			}
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('asc-nav-open')) {
				setOpen(false);
				burger.focus();
			}
		});
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1100) {
				setOpen(false);
			}
		});
	}

	/* ---------- Filter pracovných ponúk ---------- */
	document.querySelectorAll('[data-asc-jobs]').forEach(function (wrap) {
		var chips = wrap.querySelectorAll('[data-filter-trade]');
		var select = wrap.querySelector('[data-filter-country]');
		var cards = wrap.querySelectorAll('.asc-job');
		var empty = wrap.querySelector('.asc-jobs__empty');
		var state = {
			trade: wrap.getAttribute('data-pre-trade') || '',
			country: wrap.getAttribute('data-pre-country') || ''
		};

		var apply = function () {
			var visible = 0;
			cards.forEach(function (card) {
				var trades = (card.getAttribute('data-trade') || '').split(' ');
				var countries = (card.getAttribute('data-country') || '').split(' ');
				var ok = (!state.trade || trades.indexOf(state.trade) > -1) &&
					(!state.country || countries.indexOf(state.country) > -1);
				card.hidden = !ok;
				if (ok) {
					visible++;
				}
			});
			if (empty) {
				empty.hidden = visible > 0;
			}
			chips.forEach(function (chip) {
				var active = chip.getAttribute('data-filter-trade') === state.trade;
				chip.classList.toggle('is-active', active);
				chip.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
		};

		var syncUrl = function () {
			if (!window.history || !window.history.replaceState) {
				return;
			}
			var url = new URL(window.location.href);
			if (state.trade) { url.searchParams.set('profesia', state.trade); } else { url.searchParams.delete('profesia'); }
			if (state.country) { url.searchParams.set('krajina', state.country); } else { url.searchParams.delete('krajina'); }
			window.history.replaceState(null, '', url.toString());
		};

		chips.forEach(function (chip) {
			chip.addEventListener('click', function () {
				state.trade = chip.getAttribute('data-filter-trade') || '';
				apply();
				syncUrl();
			});
		});
		if (select) {
			select.addEventListener('change', function () {
				state.country = select.value;
				apply();
				syncUrl();
			});
		}
		if (state.trade || state.country) {
			apply();
			// na mobile posunúť riadok štítkov tak, aby bol aktívny filter viditeľný
			// (znova po načítaní písiem – štítky sa vtedy rozšíria)
			var revealActive = function () {
				var active = wrap.querySelector('.asc-chip.is-active');
				var bar = active ? active.parentNode : null;
				if (bar && bar.scrollWidth > bar.clientWidth) {
					bar.scrollLeft += active.getBoundingClientRect().left - bar.getBoundingClientRect().left - 16;
				}
			};
			revealActive();
			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(revealActive);
			}
			window.addEventListener('load', revealActive);
		}
	});

	/* ---------- Formuláre ---------- */
	var phoneOk = function (v) {
		return v.replace(/\D/g, '').length >= 9;
	};
	var emailOk = function (v) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);
	};

	var setError = function (field, msg) {
		var box = field.closest('.asc-field, .asc-check');
		if (!box) {
			return;
		}
		box.classList.toggle('is-invalid', !!msg);
		var err = box.querySelector('.asc-field__err');
		if (msg) {
			if (!err) {
				err = document.createElement('span');
				err.className = 'asc-field__err';
				box.appendChild(err);
			}
			err.textContent = msg;
			field.setAttribute('aria-invalid', 'true');
		} else {
			if (err) {
				err.remove();
			}
			field.removeAttribute('aria-invalid');
		}
	};

	document.querySelectorAll('.asc-form__form').forEach(function (form) {
		var msg = function (key, fallback) {
			return form.getAttribute('data-msg-' + key) || cfg.i18n[key] || fallback;
		};

		var validate = function () {
			var firstBad = null;
			form.querySelectorAll('input, select, textarea').forEach(function (field) {
				if (field.type === 'hidden' || field.name === 'asc_web') {
					return;
				}
				var val = (field.value || '').trim();
				var error = '';
				if (field.type === 'checkbox') {
					if (field.required && !field.checked) {
						error = field.name === 'asc_consent' ? msg('consent', 'Required') : msg('required', 'Required');
					}
				} else if (field.required && !val) {
					error = msg('required', 'Required');
				} else if (val && field.type === 'tel' && !phoneOk(val) && !field.hasAttribute('data-optional-phone')) {
					error = msg('phone', 'Invalid phone');
				} else if (val && field.type === 'email' && !emailOk(val)) {
					error = msg('email', 'Invalid e-mail');
				}
				setError(field, error);
				if (error && !firstBad) {
					firstBad = field;
				}
			});
			if (firstBad) {
				// do stredu obrazovky, aby pole nezakryla prilepená hlavička
				var box = firstBad.closest('.asc-field, .asc-check') || firstBad;
				box.scrollIntoView({ behavior: 'smooth', block: 'center' });
				firstBad.focus({ preventScroll: true });
			}
			return !firstBad;
		};

		form.addEventListener('input', function (e) {
			if (e.target.getAttribute('aria-invalid')) {
				setError(e.target, '');
			}
		});
		form.addEventListener('change', function (e) {
			if (e.target.getAttribute('aria-invalid')) {
				setError(e.target, '');
			}
		});

		form.addEventListener('submit', function (e) {
			if (!validate()) {
				e.preventDefault();
				return;
			}
			if (!window.fetch || !window.FormData) {
				return; // klasické odoslanie
			}
			e.preventDefault();
			var btn = form.querySelector('[type="submit"]');
			var label = btn ? btn.querySelector('span') : null;
			var original = label ? label.textContent : '';
			var out = form.querySelector('.asc-form__msg');
			if (btn) {
				btn.disabled = true;
			}
			if (label) {
				label.textContent = msg('sending', '…');
			}
			if (out) {
				out.textContent = '';
				out.classList.remove('is-error');
			}

			var data = new FormData(form);
			data.append('asc_ajax', '1');

			fetch(form.getAttribute('action') || cfg.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
				.then(function (res) {
					return res.json().catch(function () {
						return { success: false, data: {} };
					});
				})
				.then(function (json) {
					if (json && json.success) {
						var wrap = form.closest('.asc-form');
						var body = wrap ? wrap.querySelector('.asc-form__body') : null;
						var ok = wrap ? wrap.querySelector('.asc-form__success') : null;
						if (body && ok) {
							body.hidden = true;
							ok.hidden = false;
							ok.setAttribute('tabindex', '-1');
							ok.focus({ preventScroll: true });
							ok.scrollIntoView({ behavior: 'smooth', block: 'center' });
						}
						// Meranie konverzií (Google Tag Manager / GA4 / Meta Pixel cez GTM).
						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({
							event: 'asc_form_submit',
							form_type: (form.querySelector('[name="asc_variant"]') || {}).value || 'full'
						});
						return;
					}
					var fields = (json && json.data && json.data.fields) || {};
					Object.keys(fields).forEach(function (name) {
						var field = form.querySelector('[name="' + name + '"]');
						if (field) {
							setError(field, fields[name]);
						}
					});
					if (out) {
						out.textContent = (json && json.data && json.data.message) || msg('error', 'Error');
						out.classList.add('is-error');
					}
				})
				.catch(function () {
					if (out) {
						out.textContent = msg('error', 'Error');
						out.classList.add('is-error');
					}
				})
				.then(function () {
					if (btn) {
						btn.disabled = false;
					}
					if (label) {
						label.textContent = original;
					}
				});
		});
	});

	/* ---------- Mobilná lišta: skryť, keď je na obrazovke formulár ---------- */
	var mbar = document.getElementById('asc-mbar');
	if (mbar && 'IntersectionObserver' in window) {
		var forms = document.querySelectorAll('.asc-form');
		var visibleForms = new Set();
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					visibleForms.add(entry.target);
				} else {
					visibleForms.delete(entry.target);
				}
			});
			mbar.classList.toggle('is-hidden', visibleForms.size > 0);
		}, { threshold: 0.15 });
		forms.forEach(function (f) {
			io.observe(f);
		});
	}
})();
