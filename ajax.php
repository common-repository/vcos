<?php
//Wordpress admin page
add_action('admin_menu', 'vcos_admin_page');

function vcos_admin_page(){
	global $vcos_settings;
		$vcos_settings = add_options_page(__('Admin Ajax Demo', 'vcos'),
	 	__('Admin Ajax', 'vcos'), 
	 	'manage_options', 
	 	'admin-ajax-demo', 
	 	'vcos_render_admin');
}
function vcos_render_admin(){
?>	
	<div class="wrap">
	<h2><?php _e('Admin Ajax Demo', 'vcos'); ?></h2>
	<form id="vcos-form" action="" method="POST">
	<div>
	<input type="submit" name="vcos-submit" class="button-primary" id="vcos_submit" value="<?php _e('Get Results', 'vcos');?>"/>
	<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="vcos_loading" style="display:none"/>
	</div>
	</form>
	<div id="vcos_response"></div>
	</div>
<?php
}

//LOAD JAVASCRIPT scripts etc.
function vcos_load_scripts($hook){

	global $vcos_settings;
	
	//only load on our settings page
	if( $hook != $vcos_settings )
		return;
		$file = dirname(__FILE__) . '/vcos.php';
		wp_enqueue_script('vcos-ajax', plugin_dir_url($file) .'js/test.js', array('jquery'));
		wp_localize_script('vcos-ajax', 'vcos_vars', array(
		
			'vcos_nonce' => wp_create_nonce('vcos-nonce')
		)
	);
}
add_action('admin_enqueue_scripts', 'vcos_load_scripts');

//PROCESS REQUESTS USING AJAX
function vcos_process_ajax(){
	//Security check using wp_verify_nonce
	if( !isset( $_POST['vcos_nonce'] ) || !wp_verify_nonce($_POST['vcos_nonce'], 'vcos-nonce') )
		die('Permissions check failed!');
		
		echo '<p>this is my response</p>';
	
		die();
}

add_action('wp_ajax_vcos_get_results', 'vcos_process_ajax');

?>