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

<link rel="alternate" type="application/rss+xml" title="<?php echo HCE_BLOGNAME; ?> RSS Feed suscription" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php echo HCE_BLOGNAME; ?> Atom Feed suscription" href="<?php bloginfo('atom_url'); ?>" /> 
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php
if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
wp_head(); ?>

</head>

<?php // better to use body tag as the main container ?>
<body <?php body_class(); ?>>

<nav id="top-navbar" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#top-navbar-collapse">
				<span class="sr-only">Mostrar/Ocultar menú</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="top-navbar-collapse">
			<ul id="navbar-third" class="nav navbar-nav navbar-right">
				<?php if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					global $current_user;
					get_currentuserinfo();
					$user_name = $current_user->user_login;
					$user_projects_url = get_author_posts_url($user_id);
					$user_profile_edit = "/calculo-huella-carbono?action=edit";
					$user_loginout_url = wp_logout_url($_SERVER['REQUEST_URI']);
					$user_loginout_text = "Cierra tu sesión"; ?>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Hola, <strong><?php echo $user_name ?></strong> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
						<li id="user-projects"><a href="<?php echo $user_projects_url; ?>">Mis proyectos</a></li>
						<li id="user-profile-edit"><a href="<?php echo $user_profile_edit; ?>">Editar mis datos</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo $user_loginout_url?>"><?php echo $user_loginout_text ?></a></li>
						</ul>
					</li>
				<?php } else {
					$user_loginout_url = HCE_BLOGURL."/calculo-huella-carbono/?redirect_to=".$_SERVER['REQUEST_URI'];
					$user_loginout_text = "Accede / regístrate "; ?>
					<li><a href="<?php echo $user_loginout_url?>"><span class="glyphicon glyphicon-user"></span> <?php echo $user_loginout_text ?></a></li>
				<?php } ?>
			</ul>
			<?php $location = "top-menu";
			if ( has_nav_menu( $location ) ) {
				$args = array(
					'theme_location'  => $location,
					'container' => false,
					'menu_id' => 'navbar-second',
					'menu_class' => 'nav navbar-nav navbar-right'
				);
				wp_nav_menu( $args );
			} ?>
		</div>
	</div>
</nav>

<div id="pre" class="container-fluid">
	<div class="row">
		<div id="logo" class="col-sm-3">
			<a class="pull-right" href="<?php echo HCE_BLOGURL; ?>" title="<?php echo HCE_BLOGNAME; ?>"><img src="<?php echo HCE_BLOGTHEME; ?>/images/logo.arco2.png" alt="<?php echo HCE_BLOGNAME; ?>" /></a>
		</div>
		<div class="col-sm-9">
			<div id="interviews-carousel" class="carousel slide" data-ride="carousel">

<?php
$interviews = array(
	array('María Jesús González','Dra.  Arquitecta,  presidenta  de   AxS  (Agrupación  de  Arquitectos  por  la  Sostenibilidad)','m.jesus'),
	array('Javier Serra María-Tomé','Arquitecto  por  la  Universidad  Politécnica de  Madrid  y  funcionario  de  carrera  del  Cuerpo  de Arquitectos del Estado','serra'),
	array('Cecilia Alcalá','Bióloga y PDD en el IESE','cecilia'),
	array('Jesús Abadía','Licenciado  en  Ciencias  Químicas,  Diplomado  en  Ingeniería  Ambiental  en  la  EOI  y  MBA  en  el  IE','abadia'),
	array('Javier Serra María-Tomé','Arquitecto  por  la  Universidad  Politécnica de  Madrid  y  funcionario  de  carrera  del  Cuerpo  de Arquitectos del Estado','serra.b')
);
$indicators_out = "<ol class='carousel-indicators'>";
$slides_out = "<div class='carousel-inner' role='listbox'>";
foreach ( $interviews as $count => $i ) {
	if ( $count == 0 ) { $indicators_out .= "<li data-target='#interviews-carousel' data-slide-to='".$count."' class='active'></li>"; $active = " active"; }
	else { $indicators_out .= "<li data-target='#interviews-carousel' data-slide-to='".$count."'></li>"; $active = ""; }
	$count++;
	$slides_out .= "
	<div class='item".$active."'>
		<img src='".HCE_BLOGTHEME."/images/carousel.0".$count.".jpg' alt='".$i[0]."' />
		<div class='carousel-caption'>
		<strong>".$i[0]."</strong><br />".$i[1]."<br /><a href='".HCE_BLOGTHEME."/images/carousel.pdf.0".$count.".pdf'>Descargar entrevista PDF</a>
		</div>
	</div>
	";
}
$indicators_out .= "</ol>";
$slides_out .= "</div>";
			echo $indicators_out.$slides_out;
?>
			</div>
		</div>
	</div><!-- .row -->
</div><!-- .container-fluid -->
<nav id="pre-navbar" class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#pre-navbar-collapse">
				<span class="sr-only">Mostrar/Ocultar menú</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="pre-navbar-collapse">
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
		</div>
	</div>
</nav>

<div class="container">
