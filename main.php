<?php

/*
	Plugin Name: WP APP NOTIF
	Plugin URI: https://github.com/dream-space/wp-app-notif
	Description: Wordpress Plugin to manage and send Notification for Android App. This plugin could send push notification to android user when add new post or update post.
	Version: 1.0
	Author: Dream Space
	Author URI: https://codecanyon.net/user/dream_space/portfolio
	License: GPLv3
	License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/*	
	Copyright (C) 2016  Dream Space (email : dev.dream.space@gmail.com)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define( 'ROOT_PATH', dirname(__FILE__) );
@include_once ROOT_PATH."/utils/setting.php";
@include_once ROOT_PATH."/utils/notif.php";
@include_once ROOT_PATH."/utils/tools.php";
@include_once ROOT_PATH."/utils/data.php";
@include_once ROOT_PATH."/utils/table.php";
@include_once ROOT_PATH."/utils/rest.php";

/** ------------------------ Registering All required action ------------------------ */

register_activation_hook( __FILE__, 'app_notif_main_activation' );
register_deactivation_hook( __FILE__, 'app_notif_main_deactivation' );

add_action('admin_menu', 'app_notif_main_add_menu' );
add_action('admin_init', 'app_notif_main_add_setting' );

add_filter('query_vars', 'app_notif_main_query_vars', 0);
add_action('parse_request', 'app_notif_main_parse_requests', 0);

add_action('transition_post_status', 'app_notif_main_transition_post', 10, 3);
add_action('publish_future_post', 'app_notif_main_future_post', 10, 1);

/** ------------------------ End of : Registering All required action ------------------- */


/* Actions when activation of plugin  */
function app_notif_main_activation() {
	app_notif_data_init_table_users();
	app_notif_data_init_table_logs();
}

/* Actions when de-activation of plugin */
function app_notif_main_deactivation() {
	app_notif_data_delete_logs();
}

/* Add APP Notif menu at admin panel */
function app_notif_main_add_menu() {
	add_menu_page('App Notif', 'App Notif', 'manage_options', 'wp-app-notif-dashboard', 'app_notif_tools_page_file_path', plugins_url('images/wp-app-notif-logo.png', __FILE__) );

	add_submenu_page('wp-app-notif-dashboard', 'WP APP Notif Dashboard',	'Dashboard', 	'manage_options',	'wp-app-notif-dashboard', 'app_notif_tools_page_file_path');
	add_submenu_page('wp-app-notif-dashboard', 'WP APP Notif Users', 		'Users', 		'manage_options', 	'wp-app-notif-users',   	'app_notif_tools_page_file_path');
	add_submenu_page('wp-app-notif-dashboard', 'WP APP Notif History', 		'History', 		'manage_options', 	'wp-app-notif-history',   'app_notif_tools_page_file_path');
	add_submenu_page('wp-app-notif-dashboard', 'WP APP Notif Settings',  	'Settings', 	'manage_options', 	'wp-app-notif-settings',   'app_notif_tools_page_file_path');
}

/* Add setting scheme for setting page */
function app_notif_main_add_setting(){
	app_notif_setting_init();
}

/* Register query for registration API*/
function app_notif_main_query_vars($vars){
	$vars[] = 'api-app-notif';
	return $vars;
}

/**	Handle API Requests for registration user
 *  url     : http://www.domain-wp.com/wp-app-notif=register
 *  type    : POST
 *  payload : JSON
 */
function app_notif_main_parse_requests(){
	global $wp;
	if(isset($wp->query_vars['api-app-notif'])){
		$api_app_notif = $wp->query_vars['api-app-notif'];
		if($api_app_notif == 'register'){

			$app_notif_rest = new App_Notif_Rest();
			if($app_notif_rest->get_request_method() != "POST") $app_notif_rest->response('',406);

            $security = $app_notif_rest->get_header('Security');
            $options = app_notif_main_get_option();
            if($options['use-security'] == 1 && ( !isset($security) || $options['security-code'] != $security )){
                $data = json_encode(array('status'=> 'failed', 'message'=>'invalid_security'));
                $app_notif_rest->response($data, 200);
            }
			$api_data 	 = json_decode(file_get_contents("php://input"), true);
			$regid 		 = $api_data['regid'];
			$serial 	 = $api_data['serial'];
			$device_name = $api_data['device_name'];
			$os_version  = $api_data['os_version'];

			if ($regid) {
				// insert POST request into database
				$res = app_notif_data_insert_user($regid, $device_name, $serial, $os_version);
				if($res == 1){
					$data = json_encode(array('status'=> 'success', 'message'=>'successfully registered device'));
					$app_notif_rest->response($data, 200);
				}else{
					$data = json_encode(array('status'=> 'failed', 'message'=>'failed when insert to database'));
					$app_notif_rest->response($data, 200);
				}
			}else{
				$data = json_encode(array('status'=> 'failed', 'message'=>'regid cannot null'));
				$app_notif_rest->response($data, 200);
			}
		} else if($api_app_notif == 'info'){
			$app_notif_rest = new App_Notif_Rest();
			$data = json_encode(array('status'=> 'ok', 'wp_app_notif_version'=>'1.0'));
			$app_notif_rest->response($data, 200);
		}else{
			$data = array('status'=> 'failed', 'message'=>'Invalid Parameter');
			app_notif_tools_respon_simple($data);
		}
	}
}

/* Handle notification when add/update post*/
function app_notif_main_transition_post($new_status, $old_status, $post){
	app_notif_notif_post($new_status, $old_status, $post);
}

function app_notif_main_future_post($post_id) {
    update_post_meta($post_id, 'hook_fired', 'true');
    app_notif_notif_post('publish', 'future', get_post( $post_id ));
}

function app_notif_main_get_option(){
    $options = get_option('app_notif_setting');
	if(!is_array($options)){
        $options = array(
            'app-notif-api-key' => 'Your One Signal Key',
            'app-notif-api-app' => 'Your One Signal App Id',
			'post-new' => 0, 'post-update' => 0,
            'post-new-title' => 'New Post', 'post-update-title' => 'Update Post',
            'notif-topic' => 1,
            'security-code' => "Your Security Code",
			'use-security' => 0,
		);
	}

	return $options;
}

?>
