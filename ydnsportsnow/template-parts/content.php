<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
		<?php endif; ?>

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<?php if (class_exists('MultiPostThumbnails')) : ?>
		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php MultiPostThumbnails::the_post_thumbnail(get_post_type(), 'homepage-image'); ?>
		</a><!-- .post-thumbnail -->

	<?php else : ?>
		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php twentysixteen_post_thumbnail(); ?>
		</a>
	<?php endif; ?>

	<footer class="entry-footer">
		<?php twentysixteen_entry_meta(); ?>
	</footer><!-- .entry-footer -->

	<div class="entry-content">
		<?php
			$str = get_the_content();
			if (strlen($str) > 750): // feel free to tune this amount as you please
				the_excerpt();
				if (has_excerpt()): ?>
					<a href="<?php echo get_permalink(); ?>">Continue reading</a> <?php
				endif;
			else:
				the_content();
			endif;
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
