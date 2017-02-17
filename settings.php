<?php
/* Options management */


if ( ! defined( 'ABSPATH' ) ) { 
    exit;
}

function inbox_photo_options_page() {
    add_options_page( 'inbox.photo' , 'inbox.photo' , 'manage_options' , 'inbox_photo' , 'inbox_photo_options_page_html' );
}

function inbox_photo_options_page_html() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'inboxphoto' ) );
	}
	echo '<style>.inbox-warning {background-color:yellow;padding:5px;text-align:center;}</style>';
	echo '<div class="wrap">';
	echo '<h1>'.__('Your inbox.photo environment','inboxphoto').'</h1>';
	if (  ! get_option( 'inbox_photo_slug' ) ) {
		echo '<p class="inbox-warning"><span class="dashicons dashicons-warning"></span> <strong>'. __( 'You need to be connected to a valid inbox.photo account.', 'inboxphoto' ).'</strong></p>';
	}
	else {
		echo '<h2>'.__('Documentation','inboxphoto').'</h2>';
		echo '<p>'.__('This plug-in is designed to help you link your Wordpress web site to your inbox.photo based web shop by providing shortcodes for your ordering pages.','inboxphoto').'</p>';
		echo '<p>'.__('Short code usage example for product category button: <code>[inboxphoto_button category="prints"]</code>','inboxphoto').'</p>';
		echo '<p>'.__('Short code usage example for product category button with custom text: <code>[inboxphoto_button category="prints" text="text"]</code>','inboxphoto').'</p>';
		echo '<p>'.__('Short code usage example for product: <code>[inboxphoto_button category="prints" product="1234"]</code>','inboxphoto').'</p>';
		echo '<p>'.__('Short code usage example for product snippet: <code>[inboxphoto_snippet category="calendars" product="5678"]</code>','inboxphoto').'</p>';
		echo '<p>'.__('CSS example: <code>.inboxphotobutton { background-color: lightgrey; border-radius: 5px; font-size: 85%; font-weight: bold; color: black; }</code>','inboxphoto').'</p>';
		echo '<p>'.__('A product sidebar widget is also available. Visit the <a href="widgets.php">widget section</a>.','inboxphoto').'</p>';
	}

	echo '<h2>'.__('Settings','inboxphoto').'</h2>';
	if ( get_option( 'inbox_photo_slug' ) ) {
		$url = 'https://'.get_option( 'inbox_photo_slug').'.inbox.photo/api/shop-info/';
		$array = json_decode(file_get_contents($url));
		if ( $array ) $currency = $array->iso_currency;
	}
	echo '<form method="post" action="options.php">';
	settings_fields( 'inbox-photo' );
	echo '<table>';
	if ( ! ( $array ) ) {
		echo '<tr><td colspan="2"><strong>'.__('Your shop slug is missing or incorrect.','inboxphoto').'</strong></td></tr>';
	}
	echo '<tr><td><label for="inbox_photo_slug">'.__('Your inbox.photo slug (without .inbox.photo)','inboxphoto').'</td><td><input name="inbox_photo_slug" type="text" id="inbox_photo_slug" value="'. get_option( 'inbox_photo_slug' ) .'" />.inbox.photo</label> (<span id="inboxphotourlcheck"><a href="#" onclick="CheckInboxPhotoShopURL()">'.__('check','inboxphoto').'</a></span>)</td></tr>';
	echo '<tr><td><label for="inbox_photo_button_text">'.__('Your inbox.photo button default text','inboxphoto').'</td><td><input name="inbox_photo_button_text" type="text" id="inbox_photo_button_text" value="'. get_option( 'inbox_photo_button_text' ) .'" /></label></td></tr>';
	echo '<tr><td><label for="inbox_photo_button_css">'.__('Your inbox.photo button CSS','inboxphoto').'</td><td><textarea name="inbox_photo_button_css" cols="80" id="inbox_photo_button_css">'. get_option( 'inbox_photo_button_css' ) .'</textarea></label></td></tr>';
	echo '<tr><td><label for="inbox_photo_currency">'.__('Your currency','inboxphoto').'</td><td><input name="inbox_photo_currency" type="hidden" id="inbox_photo_currency" value="'. $currency .'" /><span id="inbox_photo_currency_comment">'. get_option( 'inbox_photo_currency' ) .'</span></label></td></tr>';
	echo '</table>';
	submit_button();
	echo '</form>';
	if ( $array ) {
		echo '<a href="https://inbox.photo/dashboard/"><button>'.__('Connect to dashboard','inboxphoto').'</button></a>';
	}
	echo '</div>';
	?>
	<script>
	function CheckInboxPhotoShopURL() {
		var InboxPhotoShop = document.getElementById("inbox_photo_slug");
		var InboxPhotoShopJSONURL = 'https://'+InboxPhotoShop.value+'.inbox.photo/api/shop-info/';
		console.log (InboxPhotoShopJSONURL);
		var InboxPhotoShopJSON = jQuery.getJSON(InboxPhotoShopJSONURL, function(data) {
		  document.getElementById("inboxphotourlcheck").innerHTML='<?php echo ( __('Success. Your shop name is:','inboxphoto') ) ?> '+data.name;
		  console.log(data);
		  var currency = data.iso_currency;
		  document.getElementById("inbox_photo_currency").value=data.iso_currency;
		  document.getElementById("inbox_photo_currency").innerHTML=data.iso_currency;
		  document.getElementById("inbox_photo_currency_comment").innerHTML=data.iso_currency;
		})
		.done(function() {
		})
		.fail(function() {
			alert ('<?php echo ( __('Error: the inbox.photo slug seems incorrect. Please fix it.','inboxphoto') ) ?>');
		})
		.always(function() {
		});
	}
	</script>
	<?php
}

function register_inbox_photo_settings() {
	register_setting( 'inbox-photo', 'inbox_photo_slug' );
	register_setting( 'inbox-photo', 'inbox_photo_button_css' );
	register_setting( 'inbox-photo', 'inbox_photo_button_text' );
	register_setting( 'inbox-photo', 'inbox_photo_currency' );
}