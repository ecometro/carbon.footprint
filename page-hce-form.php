<?php
/* Template name: Formulario HCE */

// call carbon footprint form
$form_out = hce_form();

get_header();
if ( !is_user_logged_in() ) { // if user is not logged in, then login form
	$tit = "Iniciar sesión / Registrarse";
} else { $tit = get_the_title(); }
?>

<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php echo $tit; ?></h1>
</header>

<main class="row" role="main">
	<section class="col-md-12">
		<?php echo $form_out; ?>
	</section>
</main>

<?php get_footer(); ?>
