<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title single-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

 	<footer class="entry-footer footer-single">
		<?php downthefield_entry_meta(); ?>
		
	</footer><!-- .entry-footer -->

	<?php if (has_post_thumbnail()): ?>
		<div class='post-thumbnail'>
			<?php
				the_post_thumbnail();
				$caption = get_post(get_post_thumbnail_id())->post_excerpt;
				if (!empty($caption)) {
					echo "<span class='thumbnail-caption'>".$caption."</span>";
				}
			?>
		</div>
	<?php endif; ?>

	<?php if(!has_post_thumbnail()): ?>
		<div class="one-em-space"></div>
	<?php endif; ?>

	<div class="entry-content content-single">
		<?php
			the_content();

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentysixteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

			if ( '' !== get_the_author_meta( 'description' ) ) {
				get_template_part( 'template-parts/biography' );
			}
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
