<?php
/**
 * The main template file.
 * @package Dance Networks EU
 */
get_header(); ?>
<?php get_template_part( 'map_json' ); ?>

<span><div id="map-canvas"></div></span>
<div class="content container">
<h1>
	<?php bloginfo( 'name' ); ?>
</h1>

<?php $net_menu = new WP_query(array('post_type' => 'network', 'posts_per_page'=>-1, 'orderby' => 'title', 'order' => 'ASC'));

if($net_menu->have_posts()): ?>
	<select role="navigation" name="networks" id="networkSelect">
		<option selected>Show All</option>
		<?php while($net_menu->have_posts()): $net_menu->the_post();
			echo '<option id="' . get_the_ID() .'">'. get_the_title() .'</option>';
		 endwhile; 

		 wp_reset_postdata(); ?>
	</select>
<?php endif; ?>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div class="project-info">
			<?php the_content(); ?>
		</div>
    <?php endwhile; endif;?>
	<div class="network-info"></div>
</div>



<?php get_footer(); ?>