<?php
/*
Plugin Name: Webpass.io MU
Plugin URI: https://webpass.io
Description: The Webpass.io plugin lets you easily integrate Webpass.io subscriptions into your website.  Provide your visitors with a subscription-for-no-ads option, that gives them ad-free content on multiple sites.
Author: Mark Saward
Version: 1.0
Author URI: http://github.com/saward/
*/


class WebpassioMU {
	function __construct() {
		$this->addHooks();
	}

	function addHooks() {
		add_shortcode( 'webpassio_valid', array($this, 'get_valid') );
	}

	function get_valid( $atts, $content = null ) {
		$options = get_option( 'webpassio_settings' );

		if (isset($options['valid'])) {
			return $options['valid'];
		}

		return '0';
	}

	function disable_plugins($plugins) {
		global $pagenow;

		if (is_array($plugins) && get_option("WEBPASSIO_disable_plugins") == "1" && (isset($_SERVER["HTTP_WEBPASSIO_VISIT"])) && !in_array($pagenow, array("plugins.php", "update-core.php", "update.php"))) {
			// Check visit claim:
			$valid = "0";
			$options = get_option( 'webpassio_settings' );
			$public_key = $options['public_key'];
			$private_key = $options['private_key'];

			if ((strlen($public_key) > 0) && (strlen($private_key) > 0)) {
				$url = "https://api.webpass.io/v1/visit/".$_SERVER["HTTP_WEBPASSIO_VISIT"];

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_USERPWD, $public_key.":".$private_key);

				$auth = curl_exec($curl);
				if($auth)
				{
					$json = json_decode($auth, true);
					if ($json['valid'] == true) {
						$valid = "1";
					  $plugins_not_needed = array(
					   'google-publisher/GooglePublisherPlugin.php',
					  );

					  foreach ( $plugins_not_needed as $plugin ) {
					   $key = array_search( $plugin, $plugins );
					   if ( false !== $key ) {
					     unset( $plugins[ $key ] );
					   }
					  }
					}
				}
			}
		}

		$options = get_option( 'webpassio_settings' );

		$options['valid'] = $valid;
		update_option( 'webpassio_settings', $options );

	  return $plugins;
	}
}

$WebpassioMU = new WebpassioMU();

add_filter('option_active_plugins', array($WebpassioMU, 'disable_plugins'));

?>
