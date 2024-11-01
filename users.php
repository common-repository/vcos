<?php
$myposts = get_posts( $args );
						foreach ( $myposts as $post ) : setup_postdata( $post ); 
							?>
							<br><a href="<?php the_permalink();?>"><?php echo $slug;?></a></br>
							<?php
						endforeach; 
						wp_reset_postdata();
