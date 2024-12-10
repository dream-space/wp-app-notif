<?php
	$target     = 'SINGLE';
    $title 		= "";
    $content 	= "";
    $image 		= "";
    $s_regid 	= "";
?>
<div class="wrap">
	
	<h2 class='opt-title'><span id='icon-options-general' class='app-notif-options'>
		<img src="<?php echo plugins_url('wp-app_notif/images/wp-app-notif-logo.png');?>" alt=""></span>
		<?php echo __( 'APP Send Notif', 'app_notif' ); ?>
	</h2>
	
	<?php
	if(isset($_POST['send_now'])){ 
		$title 		= $_POST['notif_title'];
		$content 	= $_POST['notif_content'];
		$target 	= $_POST['target'];
		$image 		= $_POST['image'];
		$s_regid 	= $_POST['s_reg_id'];
		$check_box	= ($target == 'SINGLE') ? 'block' : 'none';
		app_notif_notif_submit($title, $content, $target, $s_regid, $image);
	} 
	?>
	
	<div align="right">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="SMF4CTJ44XZ9Y" />
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate" />
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
		</form>
	</div>
	
	<form name="notif_form" action="" id="notif_form" method="post"> 

		<div class="postbox">
		<div class="inside">
		
		<h3><?php _e('Message For Push Notifications','wp_app_notif'); ?></h3>

		<table>
			<tr>
				<td style="width:150px;"><?php _e('Message Title')?> </td>
				<td><input style="width:300px;" type="text" name="notif_title" value="<?php echo $title; ?>"></td>
			</tr>
			<tr>
				<td><?php _e('Message Content')?> </td>
				<td>
					<textarea style="width:300px;" id="notif_content" name="notif_content" type="text" rows="5"><?php echo $content; ?></textarea><br>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><i><?php _e('*Please don\'t use HTML','px_gcm'); ?></i><br></td>
			</tr>

			<tr><td><br></td></tr>
			
			<tr>
				<td><?php _e('Image Url')?> </td>
				<td><input style="width:300px;" type="text" name="image" value="<?php echo $image; ?>"></td>
			</tr>

			<tr><td><br><br><br></td></tr>

			<tr>
				<td><?php _e('Target')?></td>		
				<td>
					<form action="">
						<input type="radio" name="target" value="SINGLE" <?php echo checked('SINGLE', $target, false ); ?> > <?php _e('Single Device'); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="target" value="ALL" <?php echo checked('ALL', $target, false ); ?> > <?php _e('All Device'); ?>
					</form>
				</td>			
			<tr>
			
			<tr>
				<td><?php _e('Device RegId')?> </td>
				<td><input style="width:500px; <?php echo 'display:'.$check_box; ?>" id="s_reg_id" type="text" name="s_reg_id" disable="true" value="<?php echo $s_regid; ?>"></td>
			</tr>
			
		</table> 	  
	
		</div>
		</div>
		
		<input type="submit" value="Send Now" name="send_now" id="send_now" class="button button-primary">
	</form>
	
</div>

<script>
	var radios = document.querySelectorAll('input[type=radio][name="target"]');
	function changeHandler(event) {
		if ( this.value === 'SINGLE' ) {
			document.getElementById('s_reg_id').style.display = 'block';
		} else {
			document.getElementById('s_reg_id').style.display = 'none';
			document.getElementById('s_reg_id').value  = '';
		}
	}
	Array.prototype.forEach.call(radios, function(radio) {
	   radio.addEventListener('change', changeHandler);
	});
	
</script>