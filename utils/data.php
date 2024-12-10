<?php
/*
 * 	All the functions related database
 */


/* Transaction for Table AppNotif Users -------------------------------------------
 */
function app_notif_data_init_table_users(){
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $charset_collate = $wpdb->get_charset_collate();
    $sql =
        "CREATE TABLE IF NOT EXISTS " . $app_notif_table . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`regid` text,
		`serial` text,
		`device_name` text,
		`os_version` text,
		`created_at` bigint(30),
		PRIMARY KEY (`id`)
		) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function app_notif_data_count_users($search){
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $where 	= " ";
    if(!app_notif_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(device_name, os_version) REGEXP '".$search."' ";
    }
    $sql = "SELECT COUNT(id) FROM ".$app_notif_table." ".$where.";";
    return $wpdb->get_var($sql);
}

function app_notif_data_get_users($orderby, $order, $per_page, $paged, $search){
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $where 	= " ";
    if(!app_notif_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(device_name, os_version) REGEXP '".$search."' ";
    }
    $sql 	= "SELECT * FROM ".$app_notif_table." ".$where." ORDER BY ".$orderby." ".$order." LIMIT ".$per_page." OFFSET ".$paged;
    return $wpdb->get_results($sql, ARRAY_A);
}


function app_notif_data_insert_user($regid, $device_name, $serial, $os_version) {
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';

    $device_name = !app_notif_tools_is_empty($device_name) ? $device_name : '-';
    $serial 	 = !app_notif_tools_is_empty($serial) ? $serial : '-';
    $os_version  = !app_notif_tools_is_empty($os_version) ? $os_version : '-';
    $created_at  = time();

    $sql = "SELECT serial FROM ".$app_notif_table." WHERE serial='".$serial."';";
    $result = $wpdb->get_results($sql);

    if (!$result) {
        $sql = "INSERT INTO ".$app_notif_table." (regid, serial, device_name, os_version, created_at) 
				VALUES ('$regid', '$serial', '$device_name', '$os_version', $created_at)";
        return $wpdb->query($sql);
    } else {
        return $wpdb->update($app_notif_table, array(
            'device_name' => $device_name,
            'os_version' => $os_version,
            'regid' => $regid,
            'created_at' => $created_at
        ),
            array('serial'=>$serial)
        );

    }
}

function app_notif_data_get_all_regid() {
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $arr_regid = array();
    $sql = "SELECT regid FROM ".$app_notif_table;
    $res = $wpdb->get_results($sql);
    if ($res != false) {
        foreach($res as $row){
            array_push($arr_regid, $row->regid);
        }
    }
    return $arr_regid;
}

function app_notif_data_get_regid_by_page($limit, $offset) {
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $arr_regid = array();
    $sql = "SELECT regid FROM ".$app_notif_table." ORDER BY id DESC LIMIT ".$limit." OFFSET ".$offset;
    $res = $wpdb->get_results($sql);
    if ($res != false) {
        foreach($res as $row){
            array_push($arr_regid, $row->regid);
        }
    }
    return $arr_regid;
}

function app_notif_data_get_all_count() {
    global $wpdb;
    $app_notif_table = $wpdb->prefix.'app_notif_users';
    $sql = "SELECT COUNT(id) FROM ".$app_notif_table;
    $res_count = $wpdb->get_var($sql);
    return $res_count;
}


/* Transaction for Table AppNotif Logs --------------------------------------------------------
 */
function app_notif_data_init_table_logs(){
    global $wpdb;
    $logs_table = $wpdb->prefix.'app_notif_logs';
    $charset_collate = $wpdb->get_charset_collate();
    $sql =
        "CREATE TABLE IF NOT EXISTS " . $logs_table . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` text,
		`content` text,
		`image` text,
		`target` text,
		`event` text,
		`success` int(11),
		`failure` int(11),
		`status` text,
		`created_at` bigint(30),
		PRIMARY KEY (`id`)
		) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function app_notif_data_insert_log($title, $content, $target, $event, $status, $image){
    global $wpdb;
    $logs_table = $wpdb->prefix.'app_notif_logs';
    $cur_time = time();
    $wpdb->insert($logs_table , array(
        'title' 	=> $title ,
        'content' 	=> $content,
        'image' 	=> $image,
        'target' 	=> $target,
        'event' 	=> $event,
        'success' 	=> 0,
        'failure' 	=> 0,
        'status' 	=> $status,
        'created_at'=> $cur_time,
    ));
}

function app_notif_data_count_logs($search){
    global $wpdb;
    $logs_table = $wpdb->prefix.'app_notif_logs';
    $where 	= " ";
    if(!app_notif_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(title, content, target, event, status) REGEXP '".$search."' ";
    }
    $sql = "SELECT COUNT(id) FROM ".$logs_table." ".$where.";";
    return $wpdb->get_var($sql);
}

function app_notif_data_get_logs($orderby, $order, $per_page, $paged, $search){
    global $wpdb;
    $logs_table = $wpdb->prefix.'app_notif_logs';
    $where 	= " ";
    if(!app_notif_tools_is_empty($search)){
        $where 	= " WHERE CONCAT(title, content, target, event, status) REGEXP '".$search."' ";
    }
    $sql = "SELECT * FROM ".$logs_table." ".$where." ORDER BY ".$orderby." ".$order." LIMIT ".$per_page." OFFSET ".$paged;
    return $wpdb->get_results($sql, ARRAY_A);
}

function app_notif_data_delete_logs(){
    global $wpdb;
    $logs_table = $wpdb->prefix.'app_notif_logs';
    $sql = "DROP TABLE IF EXISTS $logs_table;";
    return $wpdb->query($sql);
}


?>