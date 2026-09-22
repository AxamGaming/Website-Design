/**
 * Customizer live preview.
 */
(function ($) {
	'use strict';

	function setVar(name, value) {
		document.documentElement.style.setProperty(name, value);
	}

	wp.customize('np_color_accent', function (v) {
		v.bind(function (to) { if (to) { setVar('--np-accent', to); } });
	});
	wp.customize('np_color_accent_dark', function (v) {
		v.bind(function (to) { if (to) { setVar('--np-accent-dark', to); } });
	});
	wp.customize('np_color_hot', function (v) {
		v.bind(function (to) { if (to) { setVar('--np-hot', to); } });
	});
	wp.customize('np_color_ink', function (v) {
		v.bind(function (to) { if (to) { setVar('--np-ink', to); } });
	});

	wp.customize('np_announce_text', function (v) {
		v.bind(function (to) {
			$('.np-topbar__announce').html(to);
			$('.np-topbar__announce').toggle(!!to);
		});
	});

	wp.customize('np_whatsapp_number', function (v) {
		v.bind(function () {
			// Number changes affect many links — refresh preview.
			wp.customize.preview.send('refresh');
		});
	});
})(jQuery);
