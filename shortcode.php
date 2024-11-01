<?php
add_shortcode( 'vcos', 'vcos_shortcode1' );
function vcos_shortcode1(){
		global $wpdb;
		if ( is_user_logged_in() ) {
		global $post;
		global $cs_base_dir;
 		$wpdb->show_errors();
 		$post_id = $post->ID;
 		$slug = get_post( $post )->post_name;
 		$type_id = get_post_type( $post );
 		$current_user = wp_get_current_user();
 		$user_id = $current_user->ID;
 		
 		$vcos_settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_settings");
 		$vcos_category = $vcos_settings->category_name;
 		
 		if(!($_REQUEST['courseid']) || !($post_id)){
 			//DISPLAY USERS' COURSES IF NO COURSEID
 			$courses = $wpdb->get_results("
 					SELECT ".$wpdb->prefix."vcos_studentcourses.IDmateria as courseid, ".$wpdb->prefix."vcos_courses.course as course, 
 					".$wpdb->prefix."vcos_courses.proid as pid  
 					FROM ".$wpdb->prefix."vcos_courses, ".$wpdb->prefix."vcos_studentcourses 
 					WHERE ".$wpdb->prefix."vcos_courses.courseid=".$wpdb->prefix."vcos_studentcourses.IDmateria 
 					AND ".$wpdb->prefix."vcos_studentcourses.IDestudiante='$user_id' 
 					AND ".$wpdb->prefix."vcos_courses.enabled='1'
 					ORDER BY ".$wpdb->prefix."vcos_courses.course ASC");
				$html.='<script>
							$(document).ready(function(){
    							$(\'table tr\').click(function(){
        							window.location = $(this).attr(\'href\');
        							return false;
    							});
							});
						</script>';
			$html.='<table width="100%"><tr><thead><th colspan="6"><h1>Mis Materias (cursando)</h1></th></thead></tr><tbody>';
			$html.='<tr><td>Materia</td><td>Evaluaciones</td><td>Prácticos</td><td>Examen Final</td><td>Nota Promedia</td></tr>';
			foreach ($courses as $course){
				$hmm = $wpdb->get_row("SELECT SUM(note) as grade, 
				COUNT(DISTINCT(date)) as times FROM ".$wpdb->prefix."vcos_studentanswers 
				WHERE courseid='".$course->courseid."' AND userid='$user_id'");
				
				$evals = $hmm->grade;
				$times = $hmm->times;
				if($evals && $times){
				$evals = round($evals/$times, 2);
				}
				$htt = $wpdb->get_row("SELECT SUM(grade) as gd, COUNT(grade)as tms FROM ".$wpdb->prefix."vcos_studenthomework WHERE courseid='".$course->courseid."' AND userid='$user_id'");
				$htp = $wpdb->get_row("SELECT SUM(grade) as tgrd FROM ".$wpdb->prefix."vcos_homework WHERE courseid='".$course->courseid."'");
				$hmk_grade = $htp->tgrd;
				if($htt->tms){
				$pracs = round($htt->gd/$hmk_grade*100,2);
				}
				
				$possible = $times * 100;
				if($total && $a)
				$evals= round($total/$tot_times, 0);
				
				$fex = $wpdb->get_row("SELECT grade FROM ".$wpdb->prefix."vcos_studentfexams WHERE courseid='".$course->courseid."' AND userid='$user_id'");
				$final_prac = $pracs * 3;
				$final_exam = $fex->grade * 6;
				$final_g = $final_prac + $final_exam + $evals;
				$final_grade = $final_g / 10;
							$html.= '<tr><td><a href="'.$slug.'/?courseid='.$course->courseid.'">'.$course->course.'</td></a>';
							$html.='<td> '.$evals.' %</td><td>'.$pracs.'%</td><td>'.$fex->grade.'%</td><td>'.$final_grade.'%</td><td></td>';
							$html.=' <div class="vcos_indent">'.$post->post_excerpt.'</div></td></tr>';
			
			}		
			$html.='</tbody></table><hr>';
			
			
			
			//DISPLAY ALL COURSES AVAILABLE
 			$courses2 = $wpdb->get_results("
 					SELECT ".$wpdb->prefix."vcos_courses.courseid as courseid, ".$wpdb->prefix."vcos_courses.course as course, 
 					".$wpdb->prefix."vcos_courses.proid as pid  
 					FROM ".$wpdb->prefix."vcos_courses
 					WHERE  ".$wpdb->prefix."vcos_courses.enabled='1' 
 					AND courseid NOT IN (SELECT ".$wpdb->prefix."vcos_studentcourses.IDmateria 
 					FROM ".$wpdb->prefix."vcos_studentcourses 
 					WHERE ".$wpdb->prefix."vcos_studentcourses.IDestudiante='$user_id' 
 					AND ".$wpdb->prefix."vcos_studentcourses.IDmateria=".$wpdb->prefix."vcos_courses.courseid)
 					ORDER BY course ASC");
						
			$html.='<table width="100%"><tr><thead><th><h1>  Materias Disponibles</h1></th></thead></tr><tbody>';
			foreach ($courses2 as $course2){
						
							$category = get_category($course2->proid);
							
							$html.= '<tr><td><a href="'.$slug.'/?courseid='.$course2->courseid.'">'.$course2->course.'</a>';
							$html.=' <div class="vcos_indent">'.$post->post_excerpt.'</div></td></tr>';
							
						
			
			
			}		
				$html.='</tbody></table>';
			
			}else{
			
	if($_REQUEST['act']=='enter_homework'){
	
	$wpdb->insert(
		"".$wpdb->prefix."vcos_studenthomework",
			array(
			'userid' => $user_id,
			'courseid' => $_REQUEST['courseid'],
			'postid' => $_REQUEST['postid'],
			'homework' => $_REQUEST['homework'],
			'grade' => '0'
			)
			
			
			
			);
	}
	if($_REQUEST['act']=='enter_exam'){
		$wpdb->insert(
		"".$wpdb->prefix."vcos_studentfexams",
			array(
			'userid' => $user_id,
			'courseid' => $_REQUEST['courseid'],
			'answers' => $_REQUEST['exam'],
			'grade' => '0'
			)
			
			
			
			);


	}	
	if($_REQUEST['act']=='exam' && $_REQUEST['courseid']){
			$html.='<h1>Examen Final</h1>';
			$res = $wpdb->get_row("
			SELECT * FROM ".$wpdb->prefix."vcos_fexams WHERE courseid='".$_REQUEST['courseid']."'
			
			");
			$html.='<p>'.$res->exam.'</p>';
			$html.='</div><form name="sd"><textarea cols="75" rows="15" name="exam"></textarea>';
			$html.='<input type="submit" name="sd" value="Enviar Examen Final">';
			$html.= '<input type="hidden" name="page" value="'.$_REQUEST['page'].'">';
			$html.='<input type="hidden" name="courseid" value="'.$_REQUEST['courseid'].'">';
			$html.='<input type="hidden" name="act" value="enter_exam">';
			$html.='</form>';
		}else{
						$post_type_data = get_post_type_object( $course->courseid );
    					$post_type_slug = $post_type_data->rewrite['slug'];
    					$course_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE courseid='".$_REQUEST['courseid']."'");
 						$course_name = $course_info->course;
 						$page_title = get_the_title($post_id);
    			
    			//WHILE NO LESSON HAS BEEN ESTABLISHED...//
    			if(!(is_numeric($_REQUEST['lesson']))){
    					if(($_REQUEST['lesson']=='register') && !(is_numeric($_REQUEST['lesson']))){
						
						
						$dtime = new DateTime($result->my_datetime);
						$now = $dtime->format("Y-m-d H:i:s");
						$wpdb->insert(	
							"".$wpdb->prefix."vcos_studentcourses",
										array(
											'IDestudiante' => $user_id,
											'IDmateria' => $_REQUEST['courseid'],
											'done' => '0',
											'date' => $now
										)
									)or die(mysql_error());
						}
    						    $html.='<div id="vcos_breadcrumb">';
    							$html.='<a href="'.$slug.'">'.$slug.'</a> /';
    							$html.=' <a href="?courseid='.$_REQUEST['courseid'].'">'.$course_name.'</a> </div>';
    							$html.='<h1>'.$course_name.'</h1>';

    					
    						
						$courseid = $_REQUEST['courseid'];
						
						$html.='<div id="vcos_listlessons">';
						$signed_up_users = $wpdb->get_row("SELECT IDestudiante FROM ".$wpdb->prefix."vcos_studentcourses 
											WHERE IDmateria = '$courseid' AND IDestudiante='$user_id'" );
											$poriid = get_userdata($course_info->ownerid);
											$pro_firstname = $poriid->user_firstname;
											$pro_lastname = $poriid->user_lastname;
						$signed = $signed_up_users->IDestudiante;
						$avatar = get_avatar($course_info->ownerid, 50);
						$creds = $course_info->credits;
						if(!$creds){
						$creds = 'ND';
						}
						if(!($signed)){
							echo '<em>Nuestros registros indican que usted no está registrado en esta materia.</br>';
							echo '  Al registrarse, automaticamente será llevado a la primera lección de la materia.</em>';
							echo '<div id="vcos_description"><h1>'.$course_info->course.'</h1><div id="vcos_teacherinfo"><span class="title">'.$avatar.' Profesor: '.$pro_firstname.' '.$pro_lastname.' </span></br>Pagina: <a href="'.$poriid->user_url.'" target="_blank">'.$poriid->user_url.'</a></br>Datos Acerca del Profesor: '.$poriid->user_description.'</br></div><h2>Datos de la Materia  - Creditos: '.$creds.'</h2><p>'.$course_info->description.'</p></div>';
							echo ' ¿Desea Registrarse ahora?</br>';
							echo '<form name=""  method="POST" action=""><input type="submit" name="sd" value="SI, deseo registrarme ahora" >  <a href="'.$slug.'">Cancelar</a> ';
							echo '<input type="hidden" name="courseid" value="'.$courseid.'">';
							echo '  <input type="hidden" name="lesson" value="register"> </form>';
						}
		
						$student_course_info = $wpdb->get_row("SELECT done 
											FROM ".$wpdb->prefix."vcos_studentcourses 
											WHERE IDmateria = '$courseid' AND IDestudiante = '$user_id'
											")or die(mysql_error());
										
 						$student_max_lesson = $student_course_info->done;
 						
 						if(empty($student_max_lesson))
 						$student_max_lesson =0;
 						
    						$category_slug = $category->slug;
							//$html.='<tr><td>'.$course->course.'</td></tr>';
							$args = array(
										'posts_per_page'   => 50,
										'offset'           => '',
										'category'         => '',
										'orderby'          => 'menu_order',
										'order'            => 'ASC',
										'include'          => '',
										'exclude'          => '',
										'meta_key'         => '',
										'meta_value'       => '',
										'post_type'        => $_REQUEST['courseid'],
										'post_mime_type'   => '',
										'post_parent'      => '',
										'post_status'      => 'publish',
										'suppress_filters' => true );
							
						
						$a=0;
						$myposts = get_posts( $args );
						$count = wp_count_posts($_REQUEST['courseid']);
						$count = $count->publish;
						
						foreach ( $myposts as $post ) : setup_postdata( $post );
						$b = $a +1;
						$psid = $post->ID;
						if($user_id && $psid && $_REQUEST['courseid']){
						$ynm = $wpdb->get_row("
							SELECT postid
							FROM ".$wpdb->prefix."vcos_homework 
							WHERE postid='".$post->ID."'
							AND courseid='".$_REQUEST['courseid']."' AND enabled='1'
							");
							}
							
							$pid = $ynm->postid;
							$settings = $wpdb->get_row("SELECT grade FROM ".$wpdb->prefix."vcos_settings");
							$vcos_grade = $settings->grade;
							$vcos_gradepass = $vcos_grade + 1;
							$yls = $wpdb->get_row("
							SELECT * FROM ".$wpdb->prefix."vcos_studentanswers WHERE userid='$user_id' AND questionid='$psid'");
							
							if($student_max_lesson==$a ){
							
								$html.='<h2>'.$b.'.- ';
								$html.='<a href="?courseid='.$_REQUEST['courseid'].'&lesson='.$b.'&postid='.$psid.'"><strong>';
								$html.=''.$post->post_title.'</strong></a></h2>';
							
							}else{
							$html.='<h2>'.$b.'.- '.$post->post_title.'</h2>';
							}
							
							if($pid==$psid){
							$html.='<div class="vcos_homework">';
							$html.='<h3>Trabajo Práctico</h3>';
							$ymm = $wpdb->get_row("
							SELECT postid, grade, homework FROM ".$wpdb->prefix."vcos_studenthomework 
							WHERE postid='".$post->ID."' 
							AND courseid='".$_REQUEST['courseid']."' 
							AND userid='$user_id'");
							$gd = $ymm->grade;
							if($ymm==FALSE){
							
								$html.='<p><a href="?courseid='.$_REQUEST['courseid'].'&lesson='.$b.'&postid='.$psid.'">Entrar a Completar el Trabajo Práctico Pendiente</a></p>'.$homew.'';
								}else{
								if($gd=='0'){
									$html.='<em>la tarea práctica ha sido enviado al profesor…aguarde para recibir su nota…</em>';								
								}else{
								
									$html.=''.$ymm->homework.'';
								}
							}
							$html.='</div>';
							}
							
							if($count==$b){
							
							if($count==$student_max_lesson){
							$ynb = $wpdb->get_row(
									"SELECT * FROM ".$wpdb->prefix."vcos_studentfexams 
									WHERE courseid='".$_REQUEST['courseid']."' AND userid='$user_id'");
									$fdone = $ynb->answers;
									if(!$fdone){
							$html.= '<h2><a href="?courseid='.$_REQUEST['courseid'].'&act=exam"><strong>---- EXAMEN FINAL ----</strong></a></h2>';
										}else{
										$html.= '<h2>---- EXAMEN FINAL ----</h2>';
										}
							}else{
							$html.= '<h2>---- EXAMEN FINAL ----</h2>';
							}}
							$a++;
						endforeach; 
						wp_reset_postdata();
						$html.="</div>";
						}else{
						
						
						$post_content = get_post($_REQUEST['postid']);
						
						$html.='<div id="vcos_breadcrumb"><a href="'.$slug.'">'.$post->post_title.'</a> /'; 
						$html.='<a href="?courseid='.$_REQUEST['courseid'].'">'.$course_name.'</a> /';
						$html.=' <a href="?courseid='.$_REQUEST['courseid'].'
							&lesson='.$_REQUEST['lesson'].'
							&postid='.$post_content->ID.'">'.$post_content->post_title.'</a></div>';
    					$html.='<h1>'.$course_name.' - '.$post_content->post_title.'</h1>';
						$yum = $wpdb->get_row("
							SELECT * FROM ".$wpdb->prefix."vcos_homework 
							WHERE courseid='".$_REQUEST['courseid']."' 
							AND postid='".$_REQUEST['postid']."'
							AND enabled='1'");
						$homework = $yum->homework;
						$grade = $yum->grade;
						
						$ress = $wpdb->get_row(
						"SELECT postid 
						FROM ".$wpdb->prefix."vcos_studenthomework 
						WHERE userid='$user_id' 
						AND courseid='".$_REQUEST['courseid']."' 
						");
						$done = $ress->postid;
						$html.='<div id="vcos_content">'.$post_content->post_content.'';
						if($homework && empty($done)){
						
						
							$html.='';
							$html.='<hr><p></p><div class="vcos_homework"><form name="homs" method="POST">';
							$html.='<h3>Esta lección contiene un trabajo practico:</h3> '.$homework.'';
							$html.='<div class="vcos_homework_footer">';
							$html.='Sugerencia: al completar el trabajo en su editor de texto favorito, copie y peque aqui';
							$html.='</div><textarea cols="47" rows="6" name="homework"></textarea>';
							$html.='<input type="submit" value="Enviar Trabajo"></br>Valor Asignado a este Trabajo:'.$grade.'';
							$html.='<input type="hidden" name="courseid" value="'.$_REQUEST['courseid'].'">';
							$html.='<input type="hidden" name="postid" value="'.$_REQUEST['postid'].'">';
							$html.='<input type="hidden" name="act" value="enter_homework"></form></div>';
						
						$html.='</div><div>
						 <em><- despues de presentar el trabajo práctico de esta lección, se podrá acceder a la evaluación.</em></div>';
						
						}else{
						//EVALUATIONS
						
						$yns = $wpdb->get_row("
						SELECT * FROM ".$wpdb->prefix."vcos_studentanswers WHERE userid='$user_id' AND questionid='".$post->ID."'
						");
						if($yns->note <=$vcos_grade){
						include 'evaluations.php';
						
						}else{
						$html.='evaluación tomada';
						}
						}	
		}
						}
		}
		
				
}else{
		$settings = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_settings");
		foreach($settings as $setting){
		$vcos_category = $setting->slug_name;
		$_welcome = $setting->welcome_txt;
		
		$vcos_grade = $setting->grade;
		}
$html.= $_welcome;
}
	return $html;
}