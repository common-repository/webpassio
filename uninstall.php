<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

if (file_exists(WPMU_PLUGIN_DIR . "/WebpassioMU.class.php")) {
	@unlink(WPMU_PLUGIN_DIR . "/WebpassioMU.class.php");
}

delete_option( 'WEBPASSIO_disable_plugins' );
delete_option( 'webpassio_settings' );

?>
