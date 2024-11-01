<?php
/*
Plugin Name: Villa Charcas Online School
Plugin URI: http://vcos.info
Description: Villa Charcas Online School was developed to establish an online teaching method for Bible teachers who wish to share their teaching online. It can be adapted however to any form of online teaching in a lesson by lesson form. 
Version:1.4.0
Author: John Wry
License: GPL2
*/

/* Copyright 2013 John Wry (email: johnwry@icloud.com)

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.
This program is distributed in teh hope that if will be useful, but WITHOUT WARRANTY, without even the implied warranty of MECHANTIBILITY of FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., S1 Franklin St, Fifth Floor, Boston, MA 021110-1301 USA
*/
//CREATE TABLES
function vcos_options_install() {
   	global $wpdb;
  	$your_db_name = DB_NAME;
  
	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$your_db_name'") != $your_db_name) 
	{
		require_once(ABSPATH .'wp-content/plugins/vcos/mysql.php');
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
 
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'vcos_options_install');

//QUERY COURSES FOR CURRENT USER WITHIN ADMIN
add_action( 'init', 'codex_custom_init' );
function codex_custom_init() {
	global $wpdb;
  	global $current_user;
  	$current_user = wp_get_current_user();
	$userid = $current_user->ID;
	//Get user's courses
  	$queries = $wpdb->get_results("SELECT courseid, course FROM ".$wpdb->prefix."vcos_courses WHERE ownerid='$userid'");
		foreach ($queries as $query){
		
			$course = $query->course;
			$slug = str_replace(" ", "-", $course);
			$slug = strtolower($slug);
			$vcos_settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_settings");
 			$vcos_category = $vcos_settings->category_name;
			$courseid = $query->courseid;
			
			$labels = array(
				'name' => __( $course ),
				'singular_name' => __( $courseid ),
				'add_new'            => 'Nueva Lección'
    			
				);
			
			$args = array( 
				'labels'=> $labels,
				'public' => true, 
				
				
				'menu_position' => 6,
				'menu_icon' => $icon,
				'show_ui' => true,
				'hierarchical' => true,
				
				'menu_icon' => ''.site_url().'/wp-content/plugins/vcos/vcos_icon.png',
    			'rewrite' => array('slug' => $slug, 'with_front'=>false, 'hierarchical'=>true),
    			'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'tags')
			
			
				);
			
			register_post_type( $courseid, $args );
			
			}
  			
  			wp_register_style( 'vcos', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
			flush_rewrite_rules();
}


add_action( 'admin_menu', 'register_vcos_menu_page' );
add_action('user_admin_menu', 'register_vcos_user_menu_page');

function register_vcos_menu_page(){
    	global $wpdb;
  		global $current_user;
  		$current_user = wp_get_current_user();
		$userid = $current_user->ID;
    	add_menu_page( 'vcOS', 'vcOS ADMIN', 'edit_theme_options', 'vcos', 'vcos_admin_menu', ''.site_url().'/wp-content/plugins/vcos/vcos_icon.png', 5 );
    	add_submenu_page('vcos', 'list', 'Ver Materias', 'manage_options', 'list', 'vcos_admin_menu');
    	add_submenu_page('vcos', 'create', 'Agregar Nuevo', 'manage_options', 'create', 'vcos_admin_menu');
    	add_submenu_page('vcos', 'editar', 'Editar', 'manage_options', 'edit', 'vcos_admin_menu');
    	
    	  	$queries = $wpdb->get_results("SELECT courseid, course FROM ".$wpdb->prefix."vcos_courses WHERE ownerid='$userid'");
		foreach ($queries as $query){
		
			$course = $query->course;
			$slug = str_replace(" ", "-", $course);
			$slug = strtolower($slug);
			$vcos_settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_settings");
 			$vcos_category = $vcos_settings->category_name;
			$courseid = $query->courseid;
			
			add_submenu_page('edit.php?post_type='.$courseid.'', 'lesson_homework', 'Trabajos Practicos', 'manage_options', 'lesson_homework', 'vcos_admin_menu');
			add_submenu_page('edit.php?post_type='.$courseid.'', 'lesson_exam', 'Examen Final', 'manage_options', 'lesson_exam', 'vcos_admin_menu');
			add_submenu_page('edit.php?post_type='.$courseid.'', 'course_view', 'Resumen', 'manage_options', 'course_view', 'vcos_admin_menu');
			
		}
		
		add_options_page('My Options', 'vcOS', 'manage_options', 'settings.php', 'vcos_settings');
		
		
}

function register_vcos_user_menu_page(){
//add_menu_page( 'vcos_user_courses', 'Mis Materias', 'edit_theme_options', 'vcos_user_courses', 'vcos_admin_menu', plugins_url( 'vcos/images/icon.png' ), 50 );

}
function vcos_admin_menu(){
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	include 'vcos_admin.php';
}



function posts_for_current_author($query) {
 
  	global $wpdb;
  	global $current_user;
  	$current_user = wp_get_current_user();
	$userid = $current_user->ID;
  	
  	//Get user's courses
  	$queries = $wpdb->get_results("SELECT courseid, course FROM ".$wpdb->prefix."vcos_courses WHERE ownerid='$userid'");
		foreach ($queries as $quer){
			$course = $quer->course;
			$courseid = $quer->courseid;
 
        	if ($query->get('post_type') == $course){
         		$query->set('orderby', 'menu_order');
          		$query->set('order', 'ASC');
        	}
  		}
  return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

//META BOX STUFF:
add_action( 'add_meta_boxes', 'add_meta_boxes' );
//add_action('add_meta_boxes', 'add_meta_boxes', 1);
function add_meta_boxes(){	
	global $wpdb;
	$queries = $wpdb->get_results("SELECT courseid, course FROM ".$wpdb->prefix."vcos_courses");
		foreach ($queries as $query){
			//add_meta_box( $query->courseid, 'Preguntas', 'cd_meta_box_cb', $query->courseid, 'normal', 'high' );
			add_meta_box( $query->courseid, 'Preguntas Asignadas a Esta Lección', 'repeatable_meta_box_display', $query->courseid, 'normal', 'high');
		
		}
}

//function add_meta_boxes() {
	//add_meta_box( 'repeatable-fields', 'Audio Playlist', 'repeatable_meta_box_display', 'post', 'normal', 'high');
//}
 
function repeatable_meta_box_display() {
	global $post;
 	global $wpdb;
	$repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);
 	$courseid = get_post_type($post);
 
	wp_nonce_field( 'repeatable_meta_box_nonce', 'repeatable_meta_box_nonce' );
?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
							$('.metabox_submit').click(function(e) {
							e.preventDefault();
							$('#publish').click();
							});
						$('#add-row').on('click', function() {
							var row = $('.empty-row.screen-reader-text').clone(true);
							row.removeClass('empty-row screen-reader-text');
							row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
							return false;
						});
						$('.remove-row').on('click', function() {
							$(this).parents('tr').remove();
							return false;
						});
			 
						$('#repeatable-fieldset-one tbody').sortable({
							opacity: 0.6,
							revert: true,
							cursor: 'move',
							handle: '.sort'
						});
					$('#prac').on('change', function(e){
        				if($(this).prop('checked'))
        				{
            				$(this).next().val(1);
        				} else {
            				$(this).next().val(0);
        				}
						});
					});
				</script>
			 <script type="text/javascript">
				<!--
				function showDiv() {
   					document.getElementById('div1').style.display = "block";
				}
				
			//-->
			</script>
		<table id="repeatable-fieldset-one" width="100%">
		<thead>
			<tr>
				<th>
			 
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
		 	if ( $repeatable_fields ) :
		 	foreach ( $repeatable_fields as $field ) {
			?>
			<tr>
		
				<td>
						<a class="sort" title="mover"><--|||||||||||--></a><h3>Pregunta</h3>
			
					<strong>&iquest;</strong> 
					<input type="text"  name="ques[]" value="<?php if($field['ques'] != '') 
					echo esc_attr( $field['ques'] ); ?>" size="70"/><strong>?</strong>
	 				<h3>Respuestas</h3>
					<blockquote>1.- 
					<input type="text"  name="ans1[]" value="<?php if ($field['ans1'] != '') 
					echo esc_attr( $field['ans1'] ); else echo ''; ?>" size="60"/></blockquote>
					<blockquote>2.- 
					<input type="text"  name="ans2[]" value="<?php if ($field['ans2'] != '') 
					echo esc_attr( $field['ans2'] ); else echo ''; ?>" size="60"/></blockquote>
					<blockquote>3.- 
					<input type="text"  name="ans3[]" value="<?php if ($field['ans3'] != '') 
					echo esc_attr( $field['ans3'] ); else echo ''; ?>" size="60"/></blockquote>
					<blockquote>4.- 
					<input type="text"  name="ans4[]" value="<?php if ($field['ans4'] != '') 
					echo esc_attr( $field['ans4'] ); else echo ''; ?>" size="60"/></blockquote>
					Indique la Respuesta Correcta: 
					<select name="answer[]">
						<option value="<?php if ($field['answer'] != '') 
						echo esc_attr( $field['answer'] ); else echo 'Respuesta 1'; ?>"><?php if ($field['answer'] != '') 
						echo esc_attr( $field['answer'] ); else echo '1'; ?></option>
						<option value="1">Respuesta 1</option>
						<option value="2">Respuesta 2</option>
						<option value="3">Respuesta 3</option>
						<option value="4">Respuesta 4</option>
					</select>
			 			<a class="remove-row" href="#" 
			 			onclick="confirm('¿Seguro? Para eliminar la pregunta por completo, no se olvide actualizar la lección');
			 			return false;">Eliminar la Pregunta</a><hr><p> </p>
		 	
		 		</td>
		
			</tr>
				<?php
				}else :
				// show a blank one
				?>
			<tr>
				<td>
					<a class="button remove-row" href="#">Eliminar la Pregunta</a>
					<strong>&iquest;</strong><input type="text"  name="ques[]" size="70"/><strong>?</strong>
 
 					<h3>Respuestas</h3>
					<blockquote>1.- <input type="text"  name="ans1[]" size="60" value="" /></blockquote>
					<blockquote>2.- <input type="text"  name="ans2[]" size="60"  value="" /></blockquote>
					<blockquote>3.- <input type="text"  name="ans3[]" size="60" value="" /></blockquote>
					<blockquote>4.- <input type="text"  name="ans4[]" size="60"  value="" /></blockquote>
					<a class="sort">||||</a>
					Indique la Respuesta Correcta: 
					<select name="answer[]">
						<option value="1">Respuesta 1</option>
						<option value="2">Respuesta 2</option>
						<option value="3">Respuesta 3</option>
						<option value="4">Respuesta 4</option>
					</select>
				</td>
			</tr>
				<?php endif; ?>
 
				<!-- empty hidden one for jQuery -->
			<tr class="empty-row screen-reader-text">
				<td>
						<a class="sort">||||</a> <a class="button remove-row" href="#">Eliminar Pregunta</a>
						<strong>&iquest;</strong><input type="text" name="ques[]" size="70"/><strong>?</strong>
	 				<h3>Respuestas</h3>
		 			<blockquote>1.- <input type="text"  name="ans1[]" size="60"  value="" /></blockquote>
		 			<blockquote>2.- <input type="text"  name="ans2[]" size="60"  value="" /></blockquote>
		 			<blockquote>3.- <input type="text"  name="ans3[]" size="60"  value="" /></blockquote>
		 			<blockquote>4.- <input type="text"  name="ans4[]" size="60"  value="" /></blockquote>
		 			Indique la Respuesta Correcta: 
		 			<select name="answer[]">
						<option value="1">Respuesta 1</option>
						<option value="2">Respuesta 2</option>
						<option value="3">Respuesta 3</option>
						<option value="4">Respuesta 4</option>
					</select>
				</td>
		
			</tr>
			<tr>
				<td>
				<p><a id="add-row" class="button" href="#">Agregar Pregunta</a></br>
				<hr>
				</td>
			</tr>
		</tbody>
	</table>
 
		
		
						<?php
						$wef = $wpdb->get_row("
						SELECT * FROM ".$wpdb->prefix."vcos_homework 
						WHERE courseid='$courseid' AND postid='".$post->ID."'");
 						$fex = $wef->homework;
 						$enabled = $wef->enabled;
 						$gd = $wef->grade;
 						if($enabled=='1'){
 						$checked = 'checked="checked"';
 						$style = '';
 						$value= '1';
 						$name ='';
 						}else{
 						$checked ='';
 						$style = 'style="display:none"';
 						$value= '0';
 						$name='name="prac"';
 						}
					
					?>
					<a class="button" onclick="showDiv()">Agregar Práctico</a>
		<div id="div1" <?php echo $style;?>>
			<table width="90%">
				<tr>
					<th><h3>Trabajo Práctico</h3></th>
				</tr>
				<tr>
					<td><div>
						<?php
							wp_editor($fex, 'listing_editor', $settings = array('textarea_name' => homework, 'textarea_rows' => 30) );
						?></div>
					</td>
					<td>
						<label for="grade">Ingrese Valor del trabajo:</label>
						<select name="grade">
						<option value="<?php echo $gd;?>"><?php echo $gd;?></option>
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="50">50</option>
						<option value="60">60</option>
						<option value="70">70</option>
						<option value="80">80</option>
						<option value="90">90</option>
						<option value="100">100</option>
						</select>
						<P></P>
						<label for="prac">Habilitado</label>
						
						<input name="prac" type="checkbox" id="prac" <?php echo $checked;?>>
						<input type="hidden" name="prac_old" value="<?php echo $enabled;?>">
						<p>Para llenar el examen <p>solo ingrese las preguntas </p>
						<p>en orden y con numero.</p>
						<p></p>
						<p><em>No se olvide "actualizar" </p>
						<p>al terminar.</em></p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<p> </p>
						<p> </p>
					</td>
				</tr>
		</table>
	</div>
		 
		</br>
		<input type="hidden" name="courseid" value="<?php echo $courseid;?>">
		<input type="submit" class="metabox_submit button-primary" value="Actualizar" /> 
		<a href="">Cancelar</a></p>
		<p></p>
		<?php
}
 
add_action('save_post', 'repeatable_meta_box_save');
function repeatable_meta_box_save($post_id) {
	global $wpdb;
	if ( ! isset( $_POST['repeatable_meta_box_nonce'] ) ||
		! wp_verify_nonce( $_POST['repeatable_meta_box_nonce'], 'repeatable_meta_box_nonce' ) )
		return;
 
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
 
	if (!current_user_can('edit_post', $post_id))
		return;
 
	$old = get_post_meta($post_id, 'repeatable_fields', true);
	$new = array();
 
 
	$ques = $_POST['ques'];
	$ans1 = $_POST['ans1'];
	$ans2 = $_POST['ans2'];
	$ans3 = $_POST['ans3'];
	$ans4 = $_POST['ans4'];
	$answer = $_POST['answer'];
 
	$count = count( $ques );
 
	for ( $i = 0; $i < $count; $i++ ) {
		if ( $ques[$i] != '' ) :
			$new[$i]['ques'] = stripslashes( strip_tags( $ques[$i] ) );
			$new[$i]['ans1'] = stripslashes( $ans1[$i] ); 
			$new[$i]['ans2'] = stripslashes( $ans2[$i] ); 
			$new[$i]['ans3'] = stripslashes( $ans3[$i] ); 
			$new[$i]['ans4'] = stripslashes( $ans4[$i] ); 
			$new[$i]['answer'] = stripslashes( $answer[$i] );// and however you want to sanitize
		endif;
	}
if($_POST['homework']){
				$wef = $wpdb->get_row("
						SELECT * FROM ".$wpdb->prefix."vcos_homework 
						WHERE courseid='".$_POST['courseid']." ' AND postid='".$post_id."'");
 		$fex = $wef->homework;
 		$pr = $wef->enabled;
 		//echo $_POST['prac_old'];
 		if($_POST['prac']==$_POST['prac_old']){
 		$prac = $pr;
 		}else{
 		$prac = $_POST['prac_old'];
 		}
 		if($fex){	
	$wpdb->update(
			"".$wpdb->prefix."vcos_homework",
				array(
				'homework' => $_POST['homework'],
				
				'grade' => $_POST['grade'],
				'enabled' => $prac
				),
				array(
				'courseid' => $_POST['courseid'],
				'postid' => $post_id
				)
				);
	}else{
	
		$wpdb->insert(
			"".$wpdb->prefix."vcos_homework",
				array(
				'courseid' => $_POST['courseid'],
				'postid' => $post_id,
				'homework' => $_POST['homework'],
				'grade' => $_POST['grade'],
				'enabled' => $prac
				)
				);
	}
}
	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'repeatable_fields', $new );
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'repeatable_fields', $old );
}
	
// enqueue and localise scripts
function vcos_load_scripts(){	
	wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
	wp_register_style( 'vcos-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style( 'vcos-style' );
			$data = array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ), 
				'vcos_nonce' => wp_create_nonce('vcos-nonce')
				);
	wp_localize_script( 'my-ajax-handle', 'the_ajax_script',  $data);
}
add_action('wp_enqueue_scripts', 'vcos_load_scripts');	
add_action( 'wp_ajax_the_ajax_hook', 'vcos_action_function' );
add_action( 'wp_ajax_nopriv_the_ajax_hook', 'the_action_function' );


// THE FUNCTION TO PROCESS AJAX REQUESTS
include_once('process.php');

function vcos_settings(){
		global $post;
		global $cs_base_dir;
 		global $wpdb;
		?>
		<table width="90%">
			<tr>
				<th>
					<h1>vcOS ADMIN - Configuraciones</h1>
				</th>
			</tr>
			<tr>
				<?php
				$settings = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_settings");
					foreach($settings as $setting){
						$vcos_category = $setting->slug_name;
						$_welcome = $setting->welcome_txt;
						$vcos_grade = $setting->grade;
					}
				?>
				<td>
					<form name="fd" method="POST">
					<ul>
					<li><label for="vcos_category">Nombre de Pagina (slug) usado por vcOS</label>
					<input type="text" name="vcos_category" value="<?php echo $vcos_category;?>"></li>
					<li><label for="vcos_grade">Nota Minima para Pasar Evaluación</label>
					<input type="text" name="vcos_grade" value="<?php echo $vcos_grade;?>" size="1"> %</li>
					<input type="hidden" name="old_category" value="<?php echo $vcos_category;?>">
					</ul>
				</td>
			</tr>
			<tr>
				<th><h2>Mensaje de Bienvenida para Visitantes</h2></th>
			</tr>
			<tr>
				<td>
					<?php
					wp_editor( $_welcome, 'listingeditor', $settings = array('textarea_name' => welcome) );
					?>
				</td>
			
			</tr>
			<tr>
				<td>
					<input type="submit" name="sud" class="button-primary" value="Cambiar"></form>
				</td>
			</tr>
		</table>
<?php
}

if($_POST['vcos_category']){
				$ins = $wpdb->update(
					''.$wpdb->prefix.'vcos_settings',
							array(
								'slug_name'=> $_POST['vcos_category'],
								'welcome_txt' => stripslashes($_POST['welcome']),
								'grade' => $_POST['vcos_grade']
								),
								array(
									'slug_name'=> $_POST['old_category']
									)
						);
}	

//AFTER AJAX DOES IT"S MAGIC, WE USE SHORTCODE FUNCTION FOR ALL TO USE//
add_shortcode( 'vcos', 'vcos_shortcode1' );
include 'shortcode.php';

//AND A COOL WIDGET
include 'widget.php';



