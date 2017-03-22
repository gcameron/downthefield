<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta name="google-site-verification" content="KSSzPjl5LCzLLUyxtL7mzvJ5Pa-wuBLpahVWZPAh1Vc" />
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<div class="site-inner">
		<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentysixteen' ); ?></a>


		<header id="site-header" class="site-header" role="banner">
			<?php if ( get_header_image() ) : ?>
				<?php
					/**
					 * Filter the default twentysixteen custom header sizes attribute.
					 *
					 * @since Twenty Sixteen 1.0
					 *
					 * @param string $custom_header_sizes sizes attribute
					 * for Custom Header. Default '(max-width: 709px) 85vw,
					 * (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px'.
					 */
					$custom_header_sizes = apply_filters( 'twentysixteen_custom_header_sizes', '(max-width: 709px) 85vw, (max-width: 909px) 81vw, (max-width: 1362px) 88vw, 1200px' );
				?>
				<div class="header-image">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<center><img src="<?php header_image(); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( get_custom_header()->attachment_id ) ); ?>" sizes="<?php echo esc_attr( $custom_header_sizes ); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></center>
					</a>
				</div><!-- .header-image -->
			<?php endif; // End header image check. ?>
			<div>
				<div class="site-branding">
					<?php $description = get_bloginfo( 'description', 'display');
					if ( $description || is_customize_preview() ) : ?>
						<center><p class="site-description"><?php echo $description; ?></p></center>
					<?php endif; ?>
				</div><!-- .site-branding -->
			</div>
			<nav id="site-navigation"><?php
				if (is_home()) {
					echo '<a id="selected-tag" href="'.home_url('/').'">Home</a>';
				} else {
					echo '<a id="tag" href="'.home_url('/').'">Home</a>';
				}
				if (is_category('recaps')) {
					echo '<a id="selected-tag" href="'.home_url('/').'category/recaps">Recaps</a>';
				} else {
					echo '<a id="tag" href="'.home_url('/').'category/recaps">Recaps</a>';
				}
				if (is_category('awards')) {
					echo '<a id="selected-tag" href="'.home_url('/').'category/awards">Awards</a>';
				} else {
					echo '<a id="tag" href="'.home_url('/').'category/awards">Awards</a>';
				}
				if (is_category('ivies')) {
					echo '<a id="selected-tag" href="'.home_url('/').'category/ivies">Around the Ivies</a>';
				} else {
					echo '<a id="tag" href="'.home_url('/').'category/ivies">Around the Ivies</a>';
				}
				echo '<a id="tag" href="http://yaledailynews.com/blog/category/sports">YDN Sports Home</a>';
				echo '<a id="tag" href="http://yaledailynews.com">Yale Daily News</a>';
			//	echo '<a id="tag" href="https://twitter.com/YDNsports">@YDNSports</a>';
				if (is_page('about')) {
	    			echo '<a id="selected-tag" href="'.home_url('/').'about/">About</a>';					
				} else {
					echo '<a id="tag" href="'.home_url('/').'about/">About</a>';
				}
				/*<div id="nav-search">
    				<i class="fa fa-search"></i>
    				<form class="search-box" action="http://yaledailynews.com/">
    					<input type="text" placeholder="Search articles and authors" value="" name="s" id="s" />
    					<input style="display:none;" type="submit" id="searchsubmit" value="Search" />
    				</form>
    			</div>*/ ?>
    		</nav>
			<div class="site-header-main">

				<?php if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) ) : ?>
					<button id="menu-toggle" class="menu-toggle"><?php _e( 'Menu', 'twentysixteen' ); ?></button>

					<div id="site-header-menu" class="site-header-menu">
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'twentysixteen' ); ?>">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'primary',
										'menu_class'     => 'primary-menu',
									 ) );
								?>
							</nav><!-- .main-navigation -->
						<?php endif; ?>

						<?php if ( has_nav_menu( 'social' ) ) : ?>
							<nav id="social-navigation" class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'twentysixteen' ); ?>">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'social',
										'menu_class'     => 'social-links-menu',
										'depth'          => 1,
										'link_before'    => '<span class="screen-reader-text">',
										'link_after'     => '</span>',
									) );
								?>
							</nav><!-- .social-navigation -->
						<?php endif; ?>
					</div><!-- .site-header-menu -->
				<?php endif; ?>
			</div><!-- .site-header-main -->
		</header><!-- .site-header -->

		<div id="content" class="site-content">
