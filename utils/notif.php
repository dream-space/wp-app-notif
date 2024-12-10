<?php

/*
* All the functions push notification to Android
*/

/* Handle submit notif from dashboard page */
function app_notif_notif_submit($title, $content, $target, $single_regid, $image){
    $valid_title 	= true;
    $valid_conten 	= true;
    $valid_target 	= true;
    $valid_image 	= true;
    $total 	        = 1;

    if(app_notif_tools_is_empty($title)){
        app_notif_tools_error_msg("Message Title Cannot empty.");
        $valid_title = false;
    }

    if(app_notif_tools_is_empty($content)){
        app_notif_tools_error_msg("Message Content Cannot empty.");
        $valid_conten = false;
    }

    if(!app_notif_tools_is_empty($image) && !app_notif_tools_validate_url($image)){
        app_notif_tools_error_msg("Invalid image url.");
        $valid_image = false;
    }

    if($target === "SINGLE"){
        if(app_notif_tools_is_empty($single_regid)){
            app_notif_tools_error_msg("Device RegId Cannot Empty for Single Device.");
            $valid_conten = false;
        }
    } elseif($target === "ALL"){
        $total = app_notif_data_get_all_count();
        if($total <= 0){
            app_notif_tools_error_msg("You have no user.");
            return;
        }
        $single_regid = "";
    }

    if($valid_title && $valid_conten && $valid_target && $valid_image){
        $message = array( 'title' => $title, 'content' => $content , 'post_id' => -1, 'image' => $image);
        $respon  = app_notif_notif_prepare_send($single_regid, $message);

        if($respon == NULL){
            app_notif_tools_error_msg("Make sure your APP Notif API KEY is correct.");
            return;
        }

        $res_msg = '<p>Success : '.$respon['status']. '<br>Message : '.$respon['msg'].'</p>';
        app_notif_tools_success_msg($res_msg);
        app_notif_data_insert_log($title, $content, $target, "CUSTOM_DASHBOARD", $respon['status'], $image);
    }
}

/*
* Send push notification add/update post
*/
function app_notif_notif_post($new_status, $old_status, $post) {
    $post_title = get_the_title($post);
    $post_id 	= $post->ID;
    $content    = $post_title;
    $title      = "";

    $options = app_notif_main_get_option();
    $is_send_notif = false;

    // on add post
    if ($old_status != 'publish' && $new_status == 'publish' && $post->post_type == 'post' && $options['post-new'] == 1) {
        $is_send_notif = true;
        $title = $options['post-new-title'];
        $event = "NEW_POST";

    } else if ($old_status == 'publish' && $new_status == 'publish' && $post->post_type == 'post' && $options['post-update'] == 1) { // on update post
        $is_send_notif = true;
        $title = $options['post-update-title'];
        $event = "UPDATE_POST";
    }

    if($is_send_notif == true){
        $image_arr = get_post_image_thumb($post);
        $message = array(
            'title'     => $title,
            'content'   => $content,
            'post_id'   => $post_id,
            'image'     => sizeof($image_arr) > 0 ? $image_arr[0] : ''
        );

        $respon  = app_notif_notif_prepare_send("", $message);

        app_notif_data_insert_log($title, $content, "ALL", $event, $respon['status'], $message['image']);
    }
}

/*
 * Get image thumbnail if available
 */
function get_post_image_thumb($post){
    $image = array();
    if (has_post_thumbnail($post->ID)){
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
    }
    return $image;
}


/*
 * Handle notification more than 1000 users
 */
function app_notif_notif_prepare_send($reg_id, $message) {
    $data = array('to' => null, 'data' => $message );

    if($reg_id != ""){
        $data['to'] = $reg_id;
        $push_response = app_notif_notif_send($data);
    } else {
        $data['to'] = "All";
        $push_response = app_notif_notif_send($data);
    }

    $resp = array('status' => 'SUCCESS', 'msg' => 'Notification sent successfully');

    if (isset($push_response['errors'])){
        $resp['msg'] = $push_response['errors'][0];
        $resp['status'] = 'FAILED';
    }

    return $resp;
}


function app_notif_notif_send($data) {
    $error = false;

    //Get Option
    $app_notif_api_key = app_notif_main_get_option()['app-notif-api-key'];
    if(empty($app_notif_api_key) || strlen($app_notif_api_key) <= 0) {
        $error = true;
        return $error;
    }
    $app_notif_api_app = app_notif_main_get_option()['app-notif-api-app'];
    if(empty($app_notif_api_app) || strlen($app_notif_api_app) <= 0) {
        $error = true;
        return $error;
    }

    $fields = array(
        'app_id' => $app_notif_api_app,
        'included_segments' => array($data['to']), 
        'include_player_ids' => array($data['to']),
        'headings' => array("en" => $data['data']['title']), 
        'contents' => array("en" => $data['data']['content']),
        'data' => $data,
        'target_channel' => 'push',
        'priority' => 1
    );

    if ($data['to'] == 'All') {
        unset($fields['include_player_ids']);
    } else {
        unset($fields['included_segments']);
    }
    
    if(isset($data['data']['image'])) {
        $fields['big_picture'] = $data['data']['image'];
    }

    $headers = array( 'Authorization: Basic '.$app_notif_api_key, 'Content-Type: application/json' );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    // Execute post
    $result = curl_exec($ch);
    // Close connection
    curl_close($ch);

    return json_decode($result, true);
}

?>
