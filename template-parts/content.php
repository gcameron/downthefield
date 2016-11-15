<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/*$id = get_post_thumbnail_id($post_type, $thumb_id, $post_id);
wp_get_attachment_url($post_thumbnail_id)*/

global $counter;
global $row_mobile;

$top_start = just_variable( "top_start", FALSE);
$row_start = just_variable( "row_start", FALSE);

if($counter == $top_start) {
	$type = 'top';
} elseif ($counter == $row_start) {
	$type = 'left';
} elseif ($counter == $row_start + 1) {
	$type = 'center';
} elseif ($counter == $row_start + 2) {
	$type = 'right';
} else {
	$type = 'standard';
}

if ($type == 'top') : ?>
<a class="link-wrap" href="<?php the_permalink(); ?>">
	<div class="top">
	<article id="post-<?php the_ID(); ?>" <?php post_class('top'); ?>>
		<div class="post-thumbnail-top">
		<?php if (class_exists('MultiPostThumbnails')) : ?>
			<?php MultiPostThumbnails::the_post_thumbnail(get_post_type(), 'homepage-image'); ?>

		<?php else : ?>
				<?php twentysixteen_post_thumbnail(); ?>
		<?php endif; ?>
		</div>
		<div class="top-title">
		<?php
		echo '<h2 class="top-title">'.get_the_title().'</h2>';
		if (function_exists('coauthors')) {
			echo '<h3 class="top-author">By ';
			coauthors();
			echo '</h3>';
		} else {
			echo '<h3 class="top-author">By '.get_the_author().'</h3>';			
		}
		?>
		</div>

	</article>
	</div>
</a><!--

--><?php elseif ($type == 'left' || $type == 'center' || $type == 'right') : ?><!--
 --><table class="row"><tr>
	<?php if($type == 'left'): ?>
		<td class="left">
	<?php elseif($type == 'center'): ?>
		<td class="center">
	<?php elseif($type == 'right'): ?>
		<td class="right">
	<?php endif; ?>
	<a href="<?php the_permalink(); ?>" aria-hidden="true">
	<div class="container-container"><div class="row-thumbnail-container" style="background-image:url(<?php the_post_thumbnail_url(); ?>)"></div></div>
	</a></td></tr><tr><td class="row-title">
	<?php the_title( sprintf( '<h3 class="row-title"><a class="row-title" href="%s">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
	</td></tr></table><!--

--><?php else: ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('non-mobile'); ?>>

		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<header class="entry-header">
				<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
			</header>
		<?php endif; ?>

		<table class="entry"><tr><td class="content">

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<footer class="entry-footer">
			<?php twentysixteen_entry_meta(); ?>
		</footer>

		<div class="entry-content">
			<?php
				$str = get_the_content();
				if (strlen($str) > 750 || has_excerpt()): // feel free to tune this amount as you please
					the_excerpt();
				/* if (has_excerpt()): ?>
					<a href="<?php echo get_permalink(); ?>">Continue reading</a> <?php
				endif; */
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
		</div></td><td class="thumbnail">

		<a href="<?php the_permalink(); ?>" aria-hidden="true">
		<div class="container-container"><div class="main-thumbnail-container" style="background-image:url(<?php the_post_thumbnail_url(); ?>)"></div></div>
		</a></td></tr></table>


		<!--<header class="entry-header">
			<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
				<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
			<?php endif; ?>

			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header>
		<?php if (class_exists('MultiPostThumbnails')) : ?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php MultiPostThumbnails::the_post_thumbnail(get_post_type(), 'homepage-image'); ?>
			</a>

		<?php else : ?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php twentysixteen_post_thumbnail(); ?>
			</a>
		<?php endif; ?>

		<footer class="entry-footer">
			<?php twentysixteen_entry_meta(); ?>
		</footer>

		<div class="entry-content">
			<?php
				$str = get_the_content();
				if (strlen($str) > 750): // feel free to tune this amount as you please
					the_excerpt();
				/* if (has_excerpt()): ?>
					<a href="<?php echo get_permalink(); ?>">Continue reading</a> <?php
				endif; */
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
		</div>-->

	</article><!--
	--><hr class="non-mobile">
<?php endif; ?><!--

--><?php if ($counter == $row_start + 2) : ?><!--
	--><hr class="non-mobile">
<?php endif; ?><!--

--><article id="post-<?php the_ID(); ?>" <?php post_class('mobile'); ?>>
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
				if (strlen($str) > 750 || has_excerpt()): // feel free to tune this amount as you please
					the_excerpt();
				/* if (has_excerpt()): ?>
					<a href="<?php echo get_permalink(); ?>">Continue reading</a> <?php
				endif; */
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

	</article><!-- 
	--><hr class="mobile">