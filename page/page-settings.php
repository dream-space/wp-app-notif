<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='app-notif-options'>
		<img src="<?php echo plugins_url('wp-app_notif/images/wp-app-notif-logo.png');?>" alt=""></span>
		<?php echo __( 'APP Notif Settings', 'app_notif' ); ?>
	</h2>
	
	<?php if( isset($_GET['settings-updated']) ) { ?>
	<div id="message" class="updated">
		<p><strong><?php _e('Settings saved','wp_app_notif') ?></strong></p>
	</div>
	<?php } ?>
	
	<div class="postbox">
	<div class="inside">
	<form method="post" action="options.php">
		<?php settings_fields('wp-app-notif-settings-group'); ?>
		<?php do_settings_sections('wp-app_notif'); ?>
		<?php submit_button(); ?>
	</form>
	</div>
	</div>
</div>