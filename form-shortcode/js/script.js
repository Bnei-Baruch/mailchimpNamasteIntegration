jQuery(document).ready(function() {
	jQuery("#registrationForm").on('click', '.submit', submitRegister);
	jQuery("#loginForm").on('click', '.submit', submitLogin);
	jQuery("#registerUsersFromExel").on('click', registerUsersFromExel);
	
	
	function registerUsersFromExel(e) {
		e.preventDefault();
		
		$container = jQuery("#registrationForm");
		var data = {
			action : 'fromExelRregistrationFormShortcode',
		};
		function callback(data) {
			if (data && data.result){
				
			}
		}
		sendRequest(data, callback, $container);
		
	}
	/*Reset password customisation
	 jQuery("#rememberPass").on('click', rememberPass);
	
	function rememberPass(e){
		e.preventDefault();
		$container = jQuery("#loginForm");
		var data = {
			action : 'rememberRregistrationFormShortcode',
			userData : $container.serialize()
		};
		function callback(data) {
			if (data && data.result)
				$domEl.find(".errorMsg").html('Please check you mail').show();
		}
		sendRequest(data, callback, $container);
	}*/
	
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
			if (!data && !data.result)
				return;
			var redirectUrl = searchToObject().redirectUrl;
			redirectUrl = !redirectUrl ? window.location.origin : redirectUrl; 
			window.location.replace(redirectUrl);
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
		}).fail(function(jqXHR, textStatus) {
			$domEl.find(".preloader").hide();
			$domEl.find(".errorMsg").html('Error:' + textStatus + '. Please try more late.').show();
		});
	}
	function searchToObject() {
	  var pairs = window.location.search.substring(1).split("&"),
	    obj = {},
	    pair,
	    i;

	  for ( i in pairs ) {
	    if ( pairs[i] === "" ) continue;

	    pair = pairs[i].split("=");
	    obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
	  }

	  return obj;
	}
});
