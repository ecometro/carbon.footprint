<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />

<title>
<?php
	/* From twentyeleven theme
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	echo HCE_BLOGNAME;

	// Add the blog description for the home/front page.
	if ( HCE_BLOGDESC && ( is_home() || is_front_page() ) )
		echo " | " . HCE_BLOGDESC;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | Página ' . max( $paged, $page );
	?>
</title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="alternate" type="application/rss+xml" title="<?php echo HCE_BLOGNAME; ?> RSS Feed suscription" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php echo HCE_BLOGNAME; ?> Atom Feed suscription" href="<?php bloginfo('atom_url'); ?>" /> 
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php
if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
wp_head(); ?>

</head>

<?php // better to use body tag as the main container ?>
<body <?php body_class(); ?>>

<nav id="pre-navbar" class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#hce-pre-navbar-collapse">
				<span class="sr-only">Mostrar/Ocultar menú</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo HCE_BLOGURL; ?>" title="<?php echo HCE_BLOGNAME; ?>"><img src="<?php echo HCE_BLOGTHEME; ?>/images/hce-logo.png" alt="<?php echo HCE_BLOGNAME; ?>" /></a>
		</div>
		<div class="collapse navbar-collapse" id="hce-pre-navbar-collapse">
			<?php $location = "header-menu";
			if ( has_nav_menu( $location ) ) {
				$args = array(
					'theme_location'  => $location,
					'container' => false,
					'menu_id' => 'navbar-main',
					'menu_class' => 'nav navbar-nav'
				);
				wp_nav_menu( $args );
			} ?>
			<ul id="navbar-third" class="nav navbar-nav navbar-right">
				<li class="navbar-socialb"><a href="#">Sesion</a></li>
			</ul>
		</div>
	</div>
</nav>
<div class="container-fluid">
