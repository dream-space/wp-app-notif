<?php 
function app_notif_tools_is_empty($data){
	if(empty($data) || strlen(trim($data)) <= 0){
		return true;	
	}else{
		return false;
	}
}

function app_notif_tools_error_msg($msg){
	echo '<div id="message" class="error"> <p><strong>'.$msg.'</strong></p> </div>';
}

function app_notif_tools_success_msg($msg){
	echo '<div id="message" class="updated"><p><strong>'.$msg.'</strong></p></div>';
}

function app_notif_tools_respon($status, $message){
	$response['status'] = $status;
	$response['message'] = $message;
	header('content-type: application/json; charset=utf-8');
	echo json_encode($response)."\n";
	exit;
}

function app_notif_tools_respon_simple($response){
	header('content-type: application/json; charset=utf-8');
	echo json_encode($response)."\n";
	exit;
}

/*
 * Actions perform on loading of menu pages
 */
function app_notif_tools_page_file_path() {
	$screen = get_current_screen();
	if ( strpos( $screen->base, 'wp-app-notif-settings' ) !== false ) {
		include(ROOT_PATH.'/page/page-settings.php' );
	} 
	else if ( strpos( $screen->base, 'wp-app-notif-users' ) !== false ){
		include(ROOT_PATH.'/page/page-users.php' );
	}
	else if ( strpos( $screen->base, 'wp-app-notif-history' ) !== false ){
		include(ROOT_PATH.'/page/page-history.php' );
	}
	else {
		include(ROOT_PATH.'/page/page-send.php' );
	}
}

function app_notif_tools_validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}


?>