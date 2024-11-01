<?php
// register Foo_Widget widget
function register_vcos_widget() {
    
    register_widget( 'vcos_Widget' );
}
add_action( 'widgets_init', 'register_vcos_widget' );

/**
 * Adds Foo_Widget widget.
 */
class vcos_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'vcos_widget', // Base ID
			'vcos_Widget', // Name
			array( 'description' => __( 'vcOS Widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		//echo __( 'Hello, World!', 'text_domain' );
		
		global $post;
		global $cs_base_dir;
 		global $wpdb;
 		global $current_user;
 		$post_id = $post->ID;
 		$user_id = $current_user->ID;
 		$type_id = get_post_type( $post );
 		$vcos_settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vcos_settings");
 		$vcos_slug = $vcos_settings->slug_name;
 		echo '<ul>';
 		echo '<li>Tomando</li>';
 		$courses = $wpdb->get_results("SELECT ".$wpdb->prefix."vcos_studentcourses.IDmateria as courseid, ".$wpdb->prefix."vcos_courses.course as courses
 		FROM ".$wpdb->prefix."vcos_courses, ".$wpdb->prefix."vcos_studentcourses 
 		WHERE ".$wpdb->prefix."vcos_courses.courseid=".$wpdb->prefix."vcos_studentcourses.IDmateria 
 		AND ".$wpdb->prefix."vcos_studentcourses.IDestudiante='$user_id'
 		ORDER BY ".$wpdb->prefix."vcos_courses.course ASC
 		");
 		foreach ($courses as $course){
 		
 		echo '<li style="margin-left:20px;"><a href="'.$vcos_slug.'?courseid='.$course->courseid.'">'.$course->courses.'</a></li>';
 		
 		}
 		
 		echo '<li>Por Tomar</li>';
 		
 		
 			//DISPLAY COURSES IF NO COURSEID
 			$courses = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."vcos_courses WHERE enabled='1' AND courseid 
 			NOT IN (SELECT IDmateria FROM ".$wpdb->prefix."vcos_studentcourses WHERE IDestudiante='$user_id') ORDER BY course ASC");
			foreach ($courses as $course){
						$post_type_data = get_post_type_object( $course->courseid );
    					$post_type_slug = $post_type_data->rewrite['slug'];
    						$slug= $post_type_slug;
							
							
							echo '<li style="margin-left:20px;"><a href="'.$vcos_slug.'?courseid='.$course->courseid.'">'.$slug.'</a></li>';
							
						
				}
		echo '</ul>';
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Foo_Widget

?>