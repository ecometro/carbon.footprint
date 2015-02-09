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

<div id="pre" class="container">
	<div class="row">
		<div id="logo" class="col-sm-3">
			<a href="<?php echo HCE_BLOGURL; ?>" title="<?php echo HCE_BLOGNAME; ?>"><img class="img-responsive" src="<?php echo HCE_BLOGTHEME; ?>/images/logo.arco2.png" alt="<?php echo HCE_BLOGNAME; ?>" /></a>
		</div>
		<div class="col-sm-9">
			<div id="interviews-carousel" class="carousel slide" data-ride="carousel">

<?php
$interviews = array(
		array('María Jesús González','La fase de proyecto es fundamental en todo el proceso de la edificación y también en lo referido a emisiones, bien se trate de las relacionadas con la fase de construcción como de las demás. La <strong>arquitectura</strong> es ante todo una idea; de los criterios con los que sea gestada dependerá todo el proceso posterior.','m.jesus'),
	array('Javier Serra María-Tomé','En primer lugar <strong>tenemos que tomar conciencia</strong> nosotros mismos a través de la información y formación, para luego transmitirla a nuestros clientes, los promotores, y al resto de los agentes del proceso edificatorio. <strong>Hay que llevar este espíritu hasta la obra aunque sea lento y difícil.</strong>','serra'),
	array('Cecilia Alcalá','Sin información no podemos tomar decisiones, pero si medimos y comunicamos, <strong>con este conocimiento podemos todos contribuir a soluciones tecnológicas más ambientales</strong>. Si cada uno ponemos nuestro "granito de arena", podemos aportar en cada etapa de la construcción del edificio a la mejora de la huella del mismo.','cecilia'),
	array('Jesús Abadía','Creo que el papel de comunicar la información ambiental ha de ser, para los arquitectos, <strong>tan importante como hablar de los requisitos normativos, el diseño o la funcionalidad</strong> de la construcción.','abadia'),
	array('Javier Serra María-Tomé','<strong>Hemos estado ciegos ante estas emisiones</strong>, tanto por desconocer y ser insensibles a sus rangos y efectos, como por falta de herramientas asequibles que faciliten su estimación. La existencia de herramientas sencillas ha de permitir tomar decisiones acertadas, sobre todo a largo plazo, entendiendo que los edificios tienen una vida útil bastante más larga que otros bienes.','serra.b'),
	array('Teresa Batlle y Felipe Pich-Aguilera','Llegará un día que los edificios podrán ser una de las más importantes soluciones al problema ambiental. La población aumenta..., la habitabilidad mejora..., la ciudad crece..., los edificios deben considerar como prioridad su complicidad con el medio.','batlle')
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
		<strong>".$i[0]."</strong>: <em>".$i[1]."</em><br /><a href='".HCE_BLOGTHEME."/images/carousel.pdf.0".$count.".pdf'>Descargar entrevista PDF</a>
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

<div id="content" class="container">
