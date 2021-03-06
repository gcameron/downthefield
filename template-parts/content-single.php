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
	<?php
		if (is_single()):
			$sport_name = find_sport_category();
			if ($sport_name) : ?>
				<div class="row-sport"><?php echo $sport_name; ?></div>
		<?php endif; endif;
	?>


	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title single-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

 	<footer class="entry-footer footer-single">
		<?php downthefield_entry_meta(); ?>
	</footer><!-- .entry-footer -->

	<?php downthefield_post_thumbnail(); ?>

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
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
