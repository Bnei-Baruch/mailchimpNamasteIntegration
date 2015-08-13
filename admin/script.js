jQuery(document).ready(
		function() {
			getOptions();

			jQuery("#mailChimpFieldAddField").on('click', function(e) {
				mailChimpIntBuildField();
			});
			jQuery("#mailChimpField .submit").on('click', 'span', submit);
			jQuery("#mailChimpFieldList").on('click', 'span.remove',
					deleteField);
			function callbackRenderHTML(data, b, c) {
				jQuery('#mailChimpFieldList').empty();
				jQuery('#mailChimpConstant').empty();
				jQuery.each(data.data, function(key, val) {
					mailChimpIntBuildField(key, val)
				});
			}
			function getOptions() {

				var data = {
					action : 'getRregistrationFormFields',
					fieldName : "all"
				};
				sendRequest(data, callbackRenderHTML);
			}
			function submit(e) {
				// e.preventDefault();
				var data = {
					action : 'submitRregistrationFormBuilde',
					fieldName : "all",
					submitFields : jQuery("#mailChimpField").serialize()
				};
				sendRequest(data, callbackRenderHTML);
			}

			function deleteField(e) {
				jQuery(this).parent('div').remove();
			}

			function sendRequest(data, callback) {
				jQuery('#preloader').show();
				jQuery.ajax({
					type : "POST",
					url : ajaxurl,
					data : data,
					dataType : 'json'
				}).done(function(data) {
					jQuery('#preloader').hide();
					callback(data);
				});
			}

			function mailChimpIntBuildField(id, textVal) {
				var counter = jQuery('#mailChimpFieldList > div').length + 1;

				textVal = !textVal ? "" : textVal;
				id = !id ? "mailChimpField-" + counter : id;

				var tplDiv = jQuery('<div><input type="text" name="' + id
						+ '" value="' + textVal + '" required /></div>');
				switch (id) {
				case "mailchimpId":
					tplDiv.prepend('<h3>ID листа MailChimp для сайта</h3>');
					jQuery("#mailChimpConstant").prepend(tplDiv);
					break;
				case "mailChimpApiKey":
					tplDiv.prepend('<h3>API key для MailChimp</h3>');
					tplDiv.find('input').css('width', '300px');
					jQuery("#mailChimpConstant").prepend(tplDiv);
					break;
				default:
					tplDiv
					// .append('<input type="text" name="label_'+id+'" value=""
					// />')
					.append('<span class="button remove">Delete</span>');
					jQuery("#mailChimpFieldList").append(tplDiv);
					break;
				}
				return false;
			}

		});
