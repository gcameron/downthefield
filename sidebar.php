<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>
<?php if ( is_active_sidebar( 'sidebar-1' )  ) : ?>

	<?php echo '<a class="twitter" href="http://www.twitter.com/YDNsports"><p class="sidebar sidebar-title">@YDNSports</p></a>'; ?>
	<aside id="secondary" class="sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside><!-- .sidebar .widget-area -->
	
	<?php
		// update_standings();
		// update_schedule();
		schedule();
		standings();
	?>

<?php endif; ?>