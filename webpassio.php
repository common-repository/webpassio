<?php
/**
 * @package Webpassio
 * @version 1.0
 */
/*
Plugin Name: Webpass.io
Plugin URI: https://wordpress.org/plugins/webpassio/
Description: The Webpass.io plugin lets you easily integrate Webpass.io subscriptions into your website.  Provide your visitors with a subscription-for-no-ads option, that gives them ad-free content on multiple sites.
Author: Mark Saward
Version: 1.0
Author URI: http://github.com/saward/
*/

require_once(WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . "/lib/Webpassio.class.php");

function webpassio_add_action_links ( $links ) {
  $mylinks = array(
    '<a href="' . admin_url( 'options-general.php?page=webpassio' ) . '">Settings</a>',
  );
  return array_merge( $links, $mylinks );
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'webpassio_add_action_links' );

$Webpassio = new Webpassio(__FILE__);

?>
