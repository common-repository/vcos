<?php
//HANDLE EVALUATIONS
 		$repeatable_fields = get_post_meta($post_content->ID, 'repeatable_fields', true);
 		wp_nonce_field( 'repeatable_meta_box_nonce', 'repeatable_meta_box_nonce' );

			$html.='<script type="text/javascript">
			function showdiv(id){
			document.getElementById(\'eval_button\').style.display = \'none\';
			document.getElementById(id).style.display = "block";
			}
			</script>';
			$html .= '<div id="eval_button"><hr><input type="button" onclick="showdiv(\'eval\')" value="Tomar Evaluación"></div>';
			$html.='<div id="eval" style="display: 	none;"><div id="response_area">';
			$html.='<form id="theForm">';
			
	
 			if ( $repeatable_fields ){
 			$html.='<h2><a href="#eval" name="eval">Evaluaci&oacute;n</a></h2><hr>';
 				foreach ( $repeatable_fields as $field ) {
					$html.= '<div class="vcos_questions">¿'; 
					
					//retrieve the question
					if($field['ques'] != "")  
						$html.= esc_attr( $field['ques'] );
						$html.='?</div>';
 						$html.='<div class="vcos_italics">Indique la Respuesta Correcta</div>';
						
						//retrieve the 4 answers
						$html.='<div class="vcos_answers">';
						for ( $i = 1; $i <= 4; $i++ ) {
						$html.='<p>'.$i.'.-';
					
						if ($field['ans'.$i.''] != '') 
							$html.= esc_attr( $field['ans'.$i.''] ); else echo '';
							$html.='</p>';
							}
							$html.='</div>';
							//retrieve the correct answer and student answer
							$html.= '<input type="hidden" name="c_ans[]" value="'.$field['answer'].'">';
							$html.='Indique la Respuesta Correcta: <select id="answer" name="answer[]">';
							$html.='<option value="1">Respuesta 1</option>';
							$html.='<option value="2">Respuesta 2</option>';
							$html.='<option value="3">Respuesta 3</option>';
							$html.='<option value="4">Respuesta 4</option>';
							$html.='</select>';
		 					$html.='<hr><p> </p>';
	
						}
				$html.='<input id="id" name="id" value ="'.$post_content->ID.'" type="hidden" />';
				$html.='<input name="action" type="hidden" value="the_ajax_hook" /><input type="hidden" name="lesid" value="'.$_REQUEST['lesson'].'">';
				$html.='<input id="submit_button" value = "Enviar Respuestas y Ver Resultados" type="button" onClick="submit_me();" />';
				$html.='<input type="hidden" name="courseid" value="'.$_REQUEST['courseid'].'"></form><hr><p></p><p></p><p></p><p></p><p></p></div>';
				$html.= '</div><div></div>';

				
				}else{
				$html.='<div class="vcos_italics"> Ha ocurrido un error. No se pudo encontrar una evaluación para esta lección. </br> Contactesé con 					el profesor.</br> Gracias por su comprensión. <a href="'.$slug.'">Cancelar.</a></div>';
				}
								
				//$nlink = next_post_link_plus( array('order_by' => 'menu_order', 'order_2nd' => 'post_title' ) );
					
		