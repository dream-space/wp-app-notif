<?php
	$app_notif_table_users = new App_notif_Table_Users_List();
	if( isset($_POST['s']) ){
		$app_notif_table_users->prepare_items($_POST['s']);
	} else {
		$app_notif_table_users->prepare_items('');
	}
?>
<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='app-notif-options'>
		<img src="<?php echo plugins_url('wp-app_notif/images/wp-app-notif-logo.png');?>" alt=""></span>
		<?php echo __( 'APP Notif Users', 'app_notif' ); ?>
	</h2>
	
	<!--Search box display -->
	<form action="" method="post">
    <?php 
		$app_notif_table_users->search_box( __( 'Search' ), 'app_notif' ); 
		foreach ($_POST as $key => $value) { 
			if( 's' !== $key ) echo("<input type='hidden' name='$key' value='$value' />");
		}
	?>
	</form>
	
	<!--Table view display -->
	<form id="app_notif_Logs_table_list" action="" method="get">
		<!-- To ensure that the form posts back to our current page -->
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Render table display -->
		<?php $app_notif_table_users->display(); ?>
	</form>
	
</div>