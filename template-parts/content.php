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
global $paged;
global $post;

$top_start = 0;
$row_start = 1;

if($counter == $top_start && !$paged) {
	$type = 'top';
} elseif ($counter == $row_start && !$paged) {
	$type = 'left';
} elseif ($counter == $row_start + 1 && !$paged) {
	$type = 'center';
} elseif ($counter == $row_start + 2 && !$paged) {
	$type = 'right';
} else {
	$type = 'standard';
}

if ($type == 'top') : ?>
	<a class="link-wrap" href="<?php the_permalink(); ?>">
		<div class="top-article-container">
			<?php if (class_exists('MultiPostThumbnails')) {
				$imageid = MultiPostThumbnails::get_post_thumbnail_id(get_post_type(), 'homepage-image', $post->ID);
				$imageurl = wp_get_attachment_image_src($imageid,'large');
				$imageurl = $imageurl[0];
			} else {
				$imageurl = get_the_post_thumbnail_url();
			}
			echo '<div class="top-article-thumbnail" style="background-image:url('.$imageurl.')"></div>';
			?>
			<div class="top-article-title">
			<?php
			$sport_name = find_sport_category();
			if ($sport_name) : ?>
				<div class="top-article-sport-text"><?php echo $sport_name; ?></div>
			<?php endif;
			echo '<h2 class="top-article-title-text">'.get_the_title().'</h2>';
			if (function_exists('coauthors_links')) {
				ob_start();
				if (strcmp(coauthors(), 'Yale Daily News')) {
					ob_end_clean();
					echo '<h3 class="top-article-author-text">By ';
					coauthors();
					echo '</h3>';
				} else {
					ob_end_clean();
				}
			} else {
				if (strcmp(get_the_author(), 'Yale Daily News')) {
					echo '<h3 class="top-author">By '.get_the_author().'</h3>';
				}
			}
			?>
			</div>
		</div>
	</a><!--

--><?php elseif ($type == 'left' || $type == 'center' || $type == 'right') : ?><!--
--><div class="index-post-row"><tr>
	<?php if($type == 'left'): ?>
		<div class="left">
	<?php elseif($type == 'center'): ?>
		<div class="center">
	<?php elseif($type == 'right'): ?>
		<div class="right">
	<?php endif;
		if (class_exists('MultiPostThumbnails')) {
			$imageid = MultiPostThumbnails::get_post_thumbnail_id(get_post_type(), 'homepage-image', $post->ID);
			$imageurl = wp_get_attachment_image_src($imageid,'large');
			$imageurl = $imageurl[0];
		} else {
			$imageurl = get_the_post_thumbnail_url();
		} ?>
		<a href="<?php the_permalink(); ?>" aria-hidden="true">
			<div class="row-thumbnail-container-container">
				<div class="row-thumbnail-container" style="background-image:url(<?php echo $imageurl; ?>)"></div>
			</div>
		</a>
		<?php
			$sport_name = find_sport_category();
			if ($sport_name) : ?>
				<div class="row-sport"><?php echo $sport_name; ?></div>
			<?php endif; ?>
		<div class="row-title">
			<?php the_title( sprintf( '<h3 class="row-title"><a class="row-title" href="%s">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
		</div>
	</div>
	</div><!--

--><?php else: ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('non-mobile'); ?>>

		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<header class="entry-header">
				<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
			</header>
		<?php endif; ?>

		<table class="entry"><tr><td class="content">
		<?php
			$sport_name = find_sport_category();
			if ($sport_name) : ?>
				<div class="main-list-sport"><?php echo $sport_name; ?></div>
			<?php endif; ?>
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<footer class="entry-footer">
			<?php downthefield_entry_meta(); ?>
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
		<div class="main-thumbnail-container-container"><div class="main-thumbnail-container" style="background-image:url(<?php the_post_thumbnail_url(); ?>)"></div></div>
		</a></td></tr></table>
	</article><!--
 --><hr class="non-mobile">
<?php endif; ?><!--

--><?php if ($type == 'right') : ?><!--
	--><hr class="non-mobile">
<?php endif; ?><!--

--><article id="post-<?php the_ID(); ?>" <?php post_class('mobile'); ?>>
		<header class="entry-header">
			<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
				<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
			<?php endif; ?>
		<?php
			$sport_name = find_sport_category();
			if ($sport_name) : ?>
				<div class="main-list-sport"><?php echo $sport_name; ?></div>
			<?php endif; ?>
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header><!-- .entry-header -->
		<?php downthefield_post_thumbnail(); ?>
		<footer class="entry-footer">
			<?php downthefield_entry_meta(); ?>
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