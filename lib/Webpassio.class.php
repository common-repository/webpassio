<?php

class Webpassio {

	function __construct($mainFile) {
		$this->addHooks($mainFile);
	}

	function addHooks($mainFile) {
		add_action( 'admin_menu', array($this, 'add_admin_menu') );
		add_action( 'admin_init', array($this, 'settings_init') );
		add_action( 'wp_head', array($this, 'add_meta') );

		register_activation_hook($mainFile,array($this, 'activate'));
		register_deactivation_hook($mainFile, array($this, 'deactivate'));
	}

	function deactivate() {
		update_option("WEBPASSIO_disable_plugins", 2);
	}

	function activate() {
		if (!file_exists(WPMU_PLUGIN_DIR)) {
			@mkdir(WPMU_PLUGIN_DIR);
		}

		if (file_exists(WPMU_PLUGIN_DIR . "/WebpassioMU.class.php")) {
			@unlink(WPMU_PLUGIN_DIR . "/WebpassioMU.class.php");
		}

		if (file_exists(WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . "/WebpassioMU.class.php")) {
			@copy(WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . "/WebpassioMU.class.php", WPMU_PLUGIN_DIR . "/WebpassioMU.class.php");
		}

		if (get_option('WEBPASSIO_disable_plugins') != "1") {
			update_option('WEBPASSIO_disable_plugins', 1);
		}
	}

	function add_admin_menu(  ) {
		add_options_page( 'Webpass.io', 'Webpass.io', 'manage_options', 'webpassio', array($this, 'webpassio_options_page') );
	}


	function settings_init(  ) {
		register_setting( 'pluginPage', 'webpassio_settings' );

		add_settings_section(
			'webpassio_pluginPage_section',
			__( 'Your section description', 'wordpress' ),
			array($this, 'webpassio_settings_section_callback'),
			'pluginPage'
		);

		add_settings_field(
			'public_key',
			__( 'Verification/API Public Key', 'wordpress' ),
			array($this, 'public_key_render'),
			'pluginPage',
			'webpassio_pluginPage_section'
		);

		add_settings_field(
			'private_key',
			__( 'API Secret Key', 'wordpress' ),
			array($this, 'private_key_render'),
			'pluginPage',
			'webpassio_pluginPage_section'
		);
	}


	function public_key_render(  ) {

		$options = get_option( 'webpassio_settings' );
		?>
		<input type='text' name='webpassio_settings[public_key]' size='35' value='<?php echo $options['public_key']; ?>'>
		<?php

	}


	function private_key_render(  ) {

		$options = get_option( 'webpassio_settings' );
		?>
		<input type='text' name='webpassio_settings[private_key]' size='60' value='<?php echo $options['private_key']; ?>'>
		<?php

	}

	function webpassio_settings_section_callback(  ) {
		echo __( 'If you haven\'t already, please sign up for a publisher account at <a href="https://webpass.io/creators">https://webpass.io/creators</a>.  With a publisher account, you can claim ownership of a domain.  Once you have done so, enter your verification key for this website\'s domain below, and press save.  You will then need to wait a few minutes while we verify ownership of your domain.  You can refresh <a href="https://webpass.io/account/domains">https://webpass.io/account/domains</a> to see whenther or not the domain has been verified.  Please make sure that your website is accessible at the domain name you specified.  Once that is done, you will be able to obtain your private key by clicking \'Manage\' next to your verified domain.  Please email us at <a href="mailto:support@webpass.io">support@webpass.io</a> if you run into any troubles, or if verification takes more than 10 minutes.', 'wordpress' );
	}

	function webpassio_options_page(  ) {

		?>
		<form action='options.php' method='post'>

			<h2>Webpass.io</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<?php
	}

	function add_meta()
	{
		$options = get_option( 'webpassio_settings' );
		$public = $options['public_key'];

		if (strlen($public) > 0) {
			echo '<meta name="webpassio-site-verification" content="'.$public.'">';
		}
	}
}
?>
