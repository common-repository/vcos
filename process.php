<?php
function vcos_action_function(){
	//Security check using wp_verify_nonce
	//if( !isset( $_POST['vcos_nonce'] ) || !wp_verify_nonce($_POST['vcos_nonce'], 'vcos-nonce') )
		//die('Permissions check failed!');
		global $post;
		global $cs_base_dir;
 		global $wpdb;
 		$post_id = $post->ID;
 		$slug = get_post( $post )->post_name;
 		$type_id = get_post_type( $post );
		$user_ID = get_current_user_id();
		$new = array();
		$answers = $_POST['answer'];
		$c_ans = $_POST['c_ans'];
		$count = count( $c_ans );
		$id = $_POST['id'];
		$hms = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE ID='$id'");
		$post_type = $hms->post_type;
		$lesid = $_POST['lesid'];
		$settings = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_settings");
					foreach($settings as $setting){
						$vcos_category = $setting->slug_name;
						$_welcome = $setting->welcome_txt;
						$vcos_grade = $setting->grade;
					}
		echo '<h3>Resumen de la Evaluación</h3>';
		for ( $i = 0; $i < $count; $i++ ) {
			$c='';
			if($c_ans[$i] == $answers[$i]){
				$c=1;
			}else{
				$c=0;
			}
			$num = $i + 1;

			echo ''.$num.'.- La respuesta correcta era: '.$c_ans[$i].'. Tu respuesta fue: '.$answers[$i].'</br>';
			$d += $c;
		}
		$grade = round($d/$count*100, 2);
		$dtime = new DateTime($result->my_datetime);
		$now = $dtime->format("Y-m-d H:i:s");
		$wpdb->insert(
			"".$wpdb->prefix."vcos_studentanswers",
				array(
					'userid' => $user_ID,
					'questionid' => $id,
					'courseid' => $post_type,
					'note' => $grade,
					'date' => $now
				
				)
			)or die(mysql_error());
		if($grade>=$vcos_grade){
		$wpdb->update(
					"".$wpdb->prefix."vcos_studentcourses",
					array(
						'done' => $lesid
						),
						array(
						'IDestudiante' => $user_ID,
						'IDmateria' => $_REQUEST['courseid']
						)
		
					);
			echo 'tienes '.$d.' correctos. Tu nota es <font color="green"><strong>'.$grade.'%</strong></font>.';
			
			
			 
			 $next_post = get_next_post();
					if (!empty( $next_post )): 
  					echo '<a href="';
  					echo get_permalink( $next_post->ID ); 
  					echo '">';
  					echo $next_post->post_title;
  					echo '</a>';			
					endif;
					
		
		}else{
			echo '</br> Lo sentimos. Deberá volver a estudiar la misma lección.';
			echo 'de '.$count.' tiene '.$d.' correctos. Su nota es <font color="red"><strong>'.$grade.'%</strong></font>.';
			echo ' </br></br><em> NOTA: Le hacemos recuerdo que cada vez que se toma una evaluación, afecta su nota promedia de la materia.';
			echo ' Para evitar esto, es importante conseguir un minimo de '.$vcos_grade.'%. Gracias.</em>.';
			
			
			}
				
				//echo 'tu Promedio actual es:';
				echo '<input type="button" name="sub" class="button-primary" 
				value="Continuar estudiando" onclick="window.location.href=\''.$slug.'?courseid='.$_REQUEST['courseid'].'\'"> <a href="'.$slug.'">Terminar por hoy.</a>';
			//$name = "".$name." ".$name."";
			echo '<div>Su Evaluación ha sido registrado. Gracias</div>';

		die();// wordpress may print out a spurious zero without this – can be particularly bad if using json
}
?>