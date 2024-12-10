<?php 
/* * 
 * All the functions for the settings page
 */
function app_notif_setting_init() {
	add_settings_section('app_notif_setting-section', '', 'app_notif_setting_section_callback', 'wp-app_notif');
	
	add_settings_field('app-notif-api-key', __('One Signal Key','wp_app_notif'), 'app_notif_setting_apikey_callback', 'wp-app_notif', 'app_notif_setting-section');
	add_settings_field('app-notif-api-app', __('One Signal App Id','wp_app_notif'), 'app_notif_setting_apiapp_callback', 'wp-app_notif', 'app_notif_setting-section');

	add_settings_field('post-new', __('When Add New Post','wp_app_notif'), 'app_notif_setting_post_new_callback', 'wp-app_notif', 'app_notif_setting-section');
	add_settings_field('post-update', __('When Update Post','wp_app_notif'), 'app_notif_setting_post_update_callback', 'wp-app_notif', 'app_notif_setting-section');

    add_settings_field('post-new-title', __('New Post Title','wp_app_notif'), 'app_notif_setting_post_new_title_callback', 'wp-app_notif', 'app_notif_setting-section');
    add_settings_field('post-update-title', __('Update Post Title','wp_app_notif'), 'app_notif_setting_post_update_title_callback', 'wp-app_notif', 'app_notif_setting-section');

    add_settings_field('notif-topic', __('Send Notif by Topic','wp_app_notif'), 'app_notif_setting_topic_callback', 'wp-app_notif', 'app_notif_setting-section');

    add_settings_field('use-security', __('Use Security','wp_app_notif'), 'app_notif_setting_use_security_callback', 'wp-app_notif', 'app_notif_setting-section');
    add_settings_field('security-code', __('SECURITY CODE','wp_app_notif'), 'app_notif_setting_security_callback', 'wp-app_notif', 'app_notif_setting-section');

	register_setting('wp-app-notif-settings-group', 'app_notif_setting', 'app_notif_setting_validate' );
}

function app_notif_setting_section_callback() { }

function app_notif_setting_apikey_callback() {
    $options = app_notif_main_get_option();
    $html = '<input type="text" name="app_notif_setting[app-notif-api-key]" size="100" value="'. $options['app-notif-api-key'] .'" /> <hr/>';
    echo $html;
}

function app_notif_setting_apiapp_callback() {
    $options = app_notif_main_get_option();
    $html = '<input type="text" name="app_notif_setting[app-notif-api-app]" size="100" value="'. $options['app-notif-api-app'] .'" /> <hr/>';
    echo $html;
}

function app_notif_setting_post_new_callback(){
    $options = app_notif_main_get_option();
	$html = '<input type="checkbox" id="post-new" name="app_notif_setting[post-new]" value="1"' . checked( 1, $options['post-new'], false ) . '/>';
	echo $html;
}

function app_notif_setting_post_update_callback(){
    $options = app_notif_main_get_option();
	$html= '<input type="checkbox" id="post-update" name="app_notif_setting[post-update]" value="1"' . checked( 1, $options['post-update'], false ) . '/>';
	echo $html;
}

function app_notif_setting_post_new_title_callback() {
    $options = app_notif_main_get_option();
    $html = '<input type="text" name="app_notif_setting[post-new-title]" size="50" value="'. $options['post-new-title'] .'" />';
    echo $html;
}

function app_notif_setting_post_update_title_callback() {
    $options = app_notif_main_get_option();
    $html = '<input type="text" name="app_notif_setting[post-update-title]" size="50" value="'. $options['post-update-title'] .'" /> <hr/>';
    echo $html;
}

function app_notif_setting_topic_callback(){
    $options = app_notif_main_get_option();
    $html = '<input type="checkbox" id="notif-topic" name="app_notif_setting[notif-topic]" value="1"' . checked( 1, $options['notif-topic'], false ) . '/>';
    echo $html;
}


function app_notif_setting_use_security_callback(){
    $options = app_notif_main_get_option();
    $html = '<input type="checkbox" id="use-security" name="app_notif_setting[use-security]" value="1"' . checked( 1, $options['use-security'], false ) . '/>';
    echo $html;
}

function app_notif_setting_security_callback() {
    $options = app_notif_main_get_option();
    $html = '<textarea name="app_notif_setting[security-code]" id="ping_sites" class="large-text code" rows="2">'.$options['security-code'].'</textarea>';
    echo $html;
}

function app_notif_setting_validate($arr_input) {
	$options = get_option('app_notif_setting');
	$options['app-notif-api-key'] = trim( $arr_input['app-notif-api-key'] );
	$options['app-notif-api-app'] = trim( $arr_input['app-notif-api-app'] );

	$options['post-new'] = trim( $arr_input['post-new'] );
	$options['post-update'] = trim( $arr_input['post-update'] );
    $options['post-new-title'] 	= trim( $arr_input['post-new-title'] );
    $options['post-update-title'] = trim( $arr_input['post-update-title'] );

    $options['notif-topic'] = trim( $arr_input['notif-topic'] );

    $options['use-security'] = trim( $arr_input['use-security'] );
    $options['security-code'] = trim( $arr_input['security-code'] );

	return $options;
}
?>