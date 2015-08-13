<?php
/*
 * function baw_create_menu() {
 * add_menu_page('BAW Plugin Settings', 'BAW Settings', 'administrator', __FILE__, 'baw_settings_page',plugins_url('/images/icon.png', __FILE__));
 *
 * //call register settings function
 * add_action( 'admin_init', 'register_mysettings' );
 * }
 */

/**
 * add plugin html and bind ottions
 */
function register_mysettings() {
	add_options_page ( 'Интеграция с MailChimp', 'Интеграция с MailChimp', 'administrator', __FILE__, 'mailchimp_integrator_options_page', plugins_url ( '/images/icon.png', __FILE__ ) );
	wp_enqueue_script ( "mailChimpIntegration-admin", MAILCHIMPINT_DIR_URL . '/admin/script.js' );
	// delete_option ( 'mailChimpFieldList' );
	// delete_option ( 'mailChimpConstant' );
	if (get_option ( 'mailChimpFieldList' ) == null) {
		add_option ( 'mailChimpFieldList', array () );
	}
	if (get_option ( 'mailChimpConstant' ) == null) {
		add_option ( 'mailChimpConstant', array (
				mailchimpId => 0,
				mailChimpApiKey => 0 
		) );
	}
}
// html for plusin options
function mailchimp_integrator_options_page() {
	?>
<div class="wrap">
	<div id="preloader"
		style="height: 100%; width: 100%; position: absolute; top: 0; left: 0; display: none; background-color: rgba(0, 0, 0, .5); font: 50px Arial; color: #fff;">LOADED</div>
	<h2>Интеграция с MailChimp</h2>
	<form id="mailChimpField">
		<div id="mailChimpConstant"></div>
		<h3>Дополнительные поля профиля</h3>
		<div id="mailChimpFieldList"></div>
		<p>
			<span id="mailChimpFieldAddField" class="button button-primary">Add
				new field</span>
		</p>
		<p class="submit">
			<span class="button-primary"><?php _e('Save Changes')?></span>
		</p>
	</form>
</div>
<?php } ?>