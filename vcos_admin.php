<?php
global $wpdb;
//process form data
//wp_create_category('My category name');
$page = $_REQUEST['page'];
if($_POST['course'] && $_POST['author'] && $page=='create' && $_REQUEST['act']=='create'){
$insert = $wpdb->insert(
			''.$wpdb->prefix.'vcos_courses',
			array(
					'courseid' => '',
					'ownerid' => $_POST['author'],
					'course' => $_POST['course'],
					'theme' => '',
					'description' => stripslashes($_POST['details']),
					'preface' => '',
					'credits' => $_POST['credits'],
					'level' => '',
					'date' => '',
					'prerequisite' => '',
					'proid' => $_POST['cat'],
					'objectives' => '',
					'enabled' => '',
					'type' => '',
					'cost' => ''
			
				)
			
		);
		wp_create_category($_POST['course'], $_POST['cat']);
		$page='list';
		}

if($_POST['courseid'] && $_POST['author'] && $_POST['act']=='update_ownerid'){
$wpdb->update(
		''.$wpdb->prefix.'vcos_courses',
			array(
					
				'ownerid' => $_POST['author']
					
				),
				array(
				'courseid' => $_POST['courseid']
				)
			);
$page='list';
}
if($_POST['courseid'] && $_POST['author'] && $_POST['act']=='update' && $page=='create'){
$wpdb->update(
		''.$wpdb->prefix.'vcos_courses',
			array(
					'courseid' => $_POST['courseid'],
					'ownerid' => $_POST['author'],
					'course' => $_POST['course'],
					'theme' => '',
					'description' => stripslashes($_POST['details']),
					'preface' => '',
					'credits' => $_POST['credits'],
					'level' => '',
					'date' => '',
					'prerequisite' => '',
					'proid' => $_POST['cat'],
					'objectives' => '',
					'enabled' => $_POST['enabled'],
					'type' => '',
					'cost' => ''
			
				),
				array(
				"courseid" => $_POST['courseid']
				
				
				)
			
			
			);
$page = 'edit';
}

if($page=='list' && $_POST['courseid'] && $_POST['act']=='enable'){

$wpdb->update(
			''.$wpdb->prefix.'vcos_courses',
			array(
			'enabled' => $_POST['enabled']
			
			),
			array(
			'courseid'=>$_POST['courseid']
			)
			);

}		

if($page=='list' && $_REQUEST['courseid'] && $_REQUEST['act']=='eliminate'){

$wpdb->delete(
			''.$wpdb->prefix.'vcos_courses',
			array(
			'courseid'=> $_REQUEST['courseid']
				)
			
			);
$page= 'list';
}

$cou = $_REQUEST['courseid'];
if($page=='edit'){
	$tab_name = 'Editar Materia';
	$courses = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE courseid='$cou'");
	foreach($courses as $course){
		$_details = $course->description;
		$_course = $course->course;
		$_courseid = $course->courseid;
		$_credits = $course->credits;
		$_ownerid = $course->ownerid;
		$_proid = $course->proid;
		
	}
	$submit = 'Editar Materia';
	$act = 'update';
}else{
$tab_name = 'Ingreso de Nueva Materia';
$submit = 'Crear Materia';
$act = 'create';
}

?>


<?php

if($page=='vcos'){
		$ccs = $wpdb->get_row("
		SELECT COUNT(IDestudiante) as courses, IDmateria FROM ".$wpdb->prefix."vcos_studentcourses 
		GROUP BY IDmateria 
		ORDER BY courses DESC");
		$max_students = $ccs->courses;
		
		$max_course = $ccs->IDmateria;
		
		$ccc = $wpdb->get_row("SELECT course FROM ".$wpdb->prefix."vcos_courses WHERE courseid='$max_course'");
		$max_studs = $ccc->course;
		$setsc = $wpdb->get_row("SELECT COUNT(courseid) as courses FROM ".$wpdb->prefix."vcos_courses");
		$num_courses = $setsc->courses;
		?>
	<h1><span><img src="<?php echo site_url();?>/wp-content/plugins/vcos/vcos_large.png" width="200"></span>
	Panel Administrativo V.C. Online School</h1>
	<p></p>
	<h2>Resumen Informativo</h2>
	<style>
		table th {
		background-color: silver;
		}
		table td {
		padding:10px;
		margin:5px;
		border:1px solid #cccccc;
		text-align: center;
		}
	</style>
	<table width="90%" cellpadding="0" cellspacing="0" style="padding:10px;margin:5px;border:1px solid #cccccc;">
		<tr>
			<th>Materia con más Inscritos</th>
			<th>Mejor Alumno</th>
			<th>Preguntas Sin Responer</th>
			<th>Materias Habilitadas</th>
		</tr>
		<tr >
			<td><?php echo $max_studs;?></td>
			<td>--</td>
			<td>--</td>
			<td><?php echo $num_courses;?></td>
		</tr>

	</table>

<?php
}

?>
<?php
//ADMIN EDIT COURSE
if($page=='create' || $page=='edit'){
	?>
	<h1><span><img src="<?php echo site_url();?>/wp-content/plugins/vcos/vcos_large.png" width="200"></span>
	Panel Administrativo V.C. Online School</h1>
	<?php
	if(!$cou && $page=='edit'){
	?>
	
	<table>
		<tr>
			<td>
				<form name="Df" method="POST"><label for="courseid">Seleccione una Materia para editar</label>
				<select name="courseid" onchange="this.form.submit();"><option value="">Elija Una Materia Para Editar</option>
				<?php
				$courses = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_courses ORDER BY course ASC");
					foreach($courses as $course){
						?>
						
						<option value="<?php echo $course->courseid;?>"><?php echo $course->course;?></option>
						
					 
					 <?php }?>
				</select>
				<input type="hidden" name="page" value="edit">
				</form>
				
			</td>
		</tr>
	
	</table>
	
		<?php
		}
		?>
		
		<div id="vcos_editor" style="border:3px solid #cccccc;width:90%;">
		
		<table width="90%"><form name="" action="" method="POST">
			<tr>
				<th colspan="4"><h3><?php echo $tab_name;?></h3></th></tr>
			<tr>
				<td><label for="course">Materia</label><input type="text" name="course" value="<?php echo $_course;?>" size="70"></td>
			</tr>
			<tr>
			
				<td><label for="details">Detalles</label>
				<?php
				wp_editor( $_details, 'listingeditor', $settings = array('textarea_name' => details) );
				?>
				
				</td><td><label for="credits">Créditos</label><select name="credits">
								<option value="<?php echo $_credits;?>"><?php echo $_credits;?></option>
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								
								</select><p>
								<label for="author">Profesor</label><?php 
				
				wp_dropdown_users(array(
									"name" => "author",
									"selected"=> $_ownerid,
									"include_selected" => "true",
									"order" => "ASC"
									
									
									
									));?></p><p><label for="cat">Menu</label>
									<?php
				$select = wp_dropdown_categories(array(
						"include_selected"=>"true",
						"selected"=>$_proid,
						
						"show_count"=>"0",
						"orderby"=>"name",
						"echo"=>"0",
						"hide_empty"=>"0",
						"hierarchical"=>"1"
						
						));
					
		echo $select;
		?></p>
			</tr>
			
		
			<tr>
				<td></td><td><input type="submit" class="button-primary" value="<?php echo $submit;?>"> <a href="">Cancelar</a></td>
			</tr>
			</tr>
			<input type="hidden" name="page" value="create">
			<input type="hidden" name="act" value="<?php echo $act;?>">
			<input type="hidden" name="enabled" value="<?php echo $course->enabled;?>">
			<input type="hidden" name="courseid" value="<?php echo $_courseid;?>">
		</form></table>
		</div>
<?php
}
//ADMIN LISTS COURSES
if($page=='list'){
		?>
		<h1><span><img src="<?php echo site_url();?>/wp-content/plugins/vcos/vcos_large.png" width="200"></span>
	Panel Administrativo V.C. Online School</h1>
		<h3>Materias</h3>
		<div id="vcos_editor" style="border:3px solid #cccccc;width:90%;">
		<table cellpadding="0" cellspacing="0" width="70%">
			<tr>
				<th>Materia</th><th colspan="3">Acción</th><th>Profesor</th><th>Habilitado</th>
				<?php 
				$courses = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_courses ORDER BY course ASC");
				foreach ($courses as $course){
								$post_type_data = get_post_type_object( $course->courseid );
		    					$post_type_slug = $post_type_data->rewrite['slug'];
		    						$slug= $post_type_slug;
		    						$enabled = $course->enabled;
						if($course->course){
						?>		
							<tr>
								<td><h3><?php echo $course->course;?></h3>  </td>
								<td><a href="?courseid=<?php echo $course->courseid;?>&page=edit">editar</a> / </td>
								<td><a href="?courseid=<?php echo $course->courseid;?>&page=list&act=eliminate" 
								onclick="return		confirm('¿Seguro?')">eliminar</a></td>
								<td><a href="<?php echo site_url();?>/wp-content/plugins/vcos/admin_head-export.php?course=<?php echo $course->course;?>" target="_blank">exportar</a>
								</td>
								<td><form name="">
									<?php 
									$user_info = get_userdata($course->ownerid);
				      				$username = $user_info->user_login;
									wp_dropdown_users(array(
									
										"name" => "author",
										"show_option_none" => $username,
										"who" => "authors"
										)
									);
									?>
								
								<input type="hidden" name="act" value="update_ownerid">
								<input type="hidden" name="courseid" value="<?php echo $course->courseid;?>">
								<input type="hidden" name="page" value="list">
								
								<input type="submit" name="subtmi" class="button-primary" value="Actualizar"></form>
								</td>
								<td>
									<?php 
									if($enabled=='1'){
									$checked = 'checked';
									$value= "0";
									}else{
									$checked = '';
									$value= "1";
									}
									?>
								<form name="dfs" method="POST">
								<input type="checkbox" name="enabled" value="<?php echo $value;?>" 
								<?php echo $checked;?> onclick="this.form.submit();">
								<input type="hidden" name="page" value="list">
								<input type="hidden" name="act" value="enable">
								<input type="hidden" name="courseid" value="<?php echo $course->courseid;?>">
								</form>
								
								</td>
								<td>
								
								</td>
							</tr>
						<?php
						}
						
					} 
					?>		
				</tr>
			</table>
		</div>
		<?php
}
//TEACHER PRACTICAL HOMEWORK
if($_REQUEST['page']=='lesson_homework' && $_REQUEST['post_type']){
	$courseid = $_REQUEST['post_type'];
	$course_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE courseid='$courseid'");
	$course = $course_info->course;
	if($_POST['grade']){
	$iuy = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_studenthomework WHERE courseid='$courseid' 
	AND postid='".$_POST['hid']."'
	AND userid='".$_POST['uid']."'");
	$hmk = $iuy->homework;
	$hmk2 = '<div id="vcos_profAnswer"><h2>Comentario del Profesor</h2>'.$_POST['homework'].'</div>';
	$hmo = "".$hmk."".stripslashes($hmk2)."";
	$wpdb->update(
	"".$wpdb->prefix."vcos_studenthomework",
			array(
			'grade' => $_POST['grade'],
			'homework' => $hmo
			),
			array(
			'courseid' => $courseid,
			'postid' => $_POST['hid'],
			'userid' => $_POST['uid']
			)
	);
	}
?>
		<h1>
			<span>
				<img src="<?php echo site_url();?>/wp-content/plugins/vcos/vcos_large.png" width="200">
			</span>
			<?php echo $course;?> - Trabajos Prácticos
		</h1>

	<ol>
<?php
if($_REQUEST['hid'] && $_REQUEST['uid']){
$ium = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_homework WHERE courseid='$courseid' AND postid='".$_REQUEST['hid']."'");
?>
<h2>Trabajo: <?php echo $ium->homework;?></h2>
<p><strong>Respuesta del alumno:</strong> <em>Sugerimos hacer correcciones en ROJO</em></p>
<?php
$oiu = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_studenthomework WHERE userid='".$_REQUEST['uid']."' AND courseid='$courseid' AND postid='".$_REQUEST['hid']."'");
?>
<div style="border:1px solid; width:70%;"><?php echo $oiu->homework;?></div>
<?php


echo '<h2>Agrese Nota y Comentarios</h2>
<form name="sd" method="POST">';

wp_editor('', 'listingeditor', $settings = array('textarea_name' => homework, 'textarea_rows' => 20) );


echo '<label for="grade">Asigne una Nota al Trabajo:</label>
<select name="grade">';
?>
<option value="<?php echo $oiu->grade;?>"><?php echo $oiu->grade;?></option>
<?php
$grade = $ium->grade;
for ( $i = 0; $i < $grade; ) {
$i = $i +10;
echo '<option value="'.$i.'">'.$i.'</option>';

}
?>
</select>
<input type="hidden" name="uid" value="<?php echo $_REQUEST['uid'];?>"
<input type="hidden" name="post_type" value="<?php echo $courseid;?>">
<input type="hidden" name="hid" value="<?php echo $_REQUEST['hid'];?>">
<input type="submit" name="sd" class="button-primary" value="Enviar Nota">
<input type="hidden" name="page" value="lesson_homework">
</form>
<?php
}else{
	$practs = $wpdb->get_results("SELECT SUBSTRING_INDEX(homework, ' ', 15) as home, postid, userid FROM ".$wpdb->prefix."vcos_studenthomework WHERE courseid='$courseid'  AND grade='0'");
		foreach($practs as $prac){
		?>
			<li><a href="edit.php?post_type=<?php echo $courseid;?>&page=lesson_homework&hid=<?php echo $prac->postid;?>&uid=<?php echo $prac->userid;?>"><?php echo $prac->home;?>...</a></li>
<?php

}
	}
	
		?>
		
	</ol>
		<?php
		
}
//TEACHER FINAL EXAM
if($_REQUEST['page']=='lesson_exam' && $_REQUEST['post_type']){
	$courseid = $_REQUEST['post_type'];
	$course_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE courseid='$courseid'");
	$course = $course_info->course;
	if($_REQUEST['act']=='exam'){
	$wef = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_fexams WHERE courseid='$courseid'");
 	$fex = $wef->exam;
 	if($fex){
 		$wpdb->update(
 				"".$wpdb->prefix."vcos_fexams",
 				array(
 			
 					'exam' => stripslashes($_POST['fexam']),
 					'grade' => $_POST['f_grade']
 					),
 						array(
 							'courseid' => $courseid
 						)
 					);
 			}else{
 				$wpdb->insert(
 				"".$wpdb->prefix."vcos_fexams",
 				array(
 				'courseid' => $courseid,
 				'exam' => stripslashes($_POST['fexam']),
 				'grade' => $_POST['f_grade']
 					)
 					);
 					}}


?>

		<h1><span><img src="<?php echo site_url();?>/wp-content/plugins/vcos/vcos_large.png" width="200"></span><?php echo $course;?> - Examen Final</h1>
		<h2>Examenes Por Corregir</h2>
		
		<?php
		if($_POST['act']=='assign'){
		$wpdb->update(
		"".$wpdb->prefix."vcos_studentfexams",
				array(
					'answers' => $_POST['fexam'],
					'grade' => $_POST['fgrade']
			
				),
					array(
						'userid' => $_POST['uid'],
						'courseid' => $_POST['courseid']
				)
		
			);
		}
	if($_REQUEST['act']=='view'){
	
			$trew = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_studentfexams 
			WHERE courseid='$courseid' AND userid='".$_REQUEST['uid']."'");
			echo $trew->answers;
			?>
			<form name="SDf" method="post">
			<p><p><hr></p><h2>Agregar Comentario</h2>
			<?php
			wp_editor( $_fexam, 'listingeditor', $settings = array('textarea_name' => fexam, 'textarea_rows' => 10) );
			?>
			<label for="fgrade">Asignar Nota</label></p>
			<select name="fgrade">
						<option value="<?php echo $trew->grade;?>"><?php echo $trew->grade;?></option>
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
			<input type="hidden" name="act" value="assign">
			<input type="hidden" name="courseid" value="<?php echo $courseid;?>">
			<input type="hidden" name="uid" value="<?php echo $_REQUEST['uid'];?>">
			<input type="submit" name="sd" value="Asignar Nota">
			</form>
			<?php
	}else{
		$res = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_studentfexams WHERE grade='0' AND courseid='$courseid'");
		foreach($res as $ro){
		
		echo '<p><a href="'.admin_url().'/edit.php?post_type='.$courseid.'&page=lesson_exam&act=view&uid='.$ro->userid.'">'.$ro->answers.'</a></p>';
		}
		?>
		<div id="div1"><form name="df" method="POST">
			<table width="100%">
				<tr>
					<th>Examen Final</th>
				</tr>
				<tr>
					<td>
						<?php
						
						$wef = $wpdb->get_row("
						SELECT * FROM ".$wpdb->prefix."vcos_fexams 
						WHERE courseid='$courseid'");
 						$fex = $wef->exam;
						if(!($fex)){
							$_fexam = '<p>1. Primera pregunta del examen final</p><p>2. Segunda pregunta del examen final</p>3. etc. etc.';
						}else{
							$_fexam = $fex;
						}
					wp_editor( $_fexam, 'listingeditor', $settings = array('textarea_name' => fexam, 'textarea_rows' => 30) );
					?>
					</td>
					<td><label for="f_grade">Valor Asignado</label><select name="f_grade">
						<option value="<?php echo $wef->grade;?>"><?php echo $wef->grade;?></option>
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
						</select><br />
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
		
		<input type="submit" class="button-primary" value="Actualizar Examen" /> 
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>">
		<input type="hidden" name="act" value="exam">
		<input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type'];?>">
		<a href="">Cancelar</a></p></form>
		<p></p>
<?php
}
}

if($_REQUEST['page']=='course_view' && $_REQUEST['post_type']){
$courseid = $_REQUEST['post_type'];
	$course_info = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE courseid='$courseid'");
	$course = $course_info->course;
	
?>
<h1>Resumen de <?php echo $course;?></h1>
<table>
<thead>
			<tr>
				<th>
			 Información de Inscritos
				</th>
			</tr>
		</thead>
	
	<tr>
		<th>Nombre</th><th>Ult. Lección</th><th>Prom. Lecciones</th><th>Prom. Tarea</th><th>Gral.</th>
	</tr>
	<?php
	$res = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_studentcourses WHERE IDmateria='$courseid'");
		foreach($res as $ro){
		$user_info = get_userdata($ro->IDestudiante);
		$first_name = $user_info->first_name;
		$last_name = $user_info->last_name;
		$user_id = $ro->IDestudiante;
				$hmm = $wpdb->get_row("SELECT SUM(note) as grade, 
				COUNT(DISTINCT(date)) as times FROM ".$wpdb->prefix."vcos_studentanswers 
				WHERE courseid='".$courseid."' AND userid='$user_id'");
				
				$evals = $hmm->grade;
				$times = $hmm->times;
				if($evals && $times){
				$evals = round($evals/$times, 2);
				}
				$htt = $wpdb->get_row("SELECT SUM(grade) as gd, COUNT(grade)as tms FROM ".$wpdb->prefix."vcos_studenthomework WHERE courseid='$courseid' AND userid='$user_id'");
				$htp = $wpdb->get_row("SELECT SUM(grade) as tgrd FROM ".$wpdb->prefix."vcos_homework WHERE courseid='$courseid'");
				$hmk_grade = $htp->tgrd;
				if($htt->tms){
				$pracs = round($htt->gd/$hmk_grade*100,2);
				}
				
				$possible = $times * 100;
				if($total && $a)
				$evals= round($total/$tot_times, 0);
				
				$fex = $wpdb->get_row("SELECT grade FROM ".$wpdb->prefix."vcos_studentfexams WHERE courseid='".$courseid."' AND userid='$user_id'");
				$final_prac = $pracs * 3;
				$final_exam = $fex->grade * 6;
				$final_g = $final_prac + $final_exam + $evals;
				$final_grade = $final_g / 10;
		if($ro->done=='0'){
		$evals = '--';
		$pracs = '--';
		$final_grade = '--';
		
		}
		?>
		<tr>
			<td><?php echo $last_name; echo ', '; echo $first_name?></td><td><?php echo $ro->done;?></td><td><?php echo $evals;?>%</td><td><?php echo $pracs;?>%</td><td><?php echo $final_grade;?>%</td>
		</tr>
		
		<?php
		}
?>
</table>
<?php
}
?>

