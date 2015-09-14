jQuery(document).ready(function() {
	jQuery("#registrationForm").on('click', '.submit', submitRegister);
	jQuery("#loginForm").on('click', '.submit', submitLogin);
	function submitRegister(event) {
		event.preventDefault();
		$container = jQuery("#registrationForm");
		var data = {
			action : 'registerRregistrationFormShortcode',
			userData : $container.serialize()
		};
		function callback(data) {
			 if (data && data.result)
			 window.location.search = "successful=true";
		}
		sendRequest(data, callback, $container);
	}
	function submitLogin(event) {
		event.preventDefault();
		$container = jQuery("#loginForm");
		var data = {
			action : 'loginRregistrationFormShortcode',
			userData : $container.serialize()
		};
		function callback(data) {
			 if (data && data.result)
			 window.location.replace(window.location.origin);
		}
		sendRequest(data, callback, $container);
	}

	function sendRequest(data, callback, $domEl) {
		$domEl.find(".errorMsg").hide();
		if ($domEl.validate)
			$domEl.validate();
		/*
		 * var errors = $domEl.validate(); if(!errors.valid()) return;
		 */
		$domEl.find(".preloader").show();
		jQuery.ajax({
			type : "POST",
			url : ajaxurl,
			data : data,
			dataType : 'json'
		}).done(function(data) {
			$domEl.find(".preloader").hide();
			if (!data.result) {
				$domEl.find(".errorMsg").html(data.error).show();
				return;
			}
			callback(data);
		});
	}
});
