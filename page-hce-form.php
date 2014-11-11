<?php
/* Template name: Formulario HCE */
get_header();

// form step
if ( array_key_exists('step', $_GET) ) { $form_step = sanitize_text_field($_GET['step']); }
else { $form_step = 1; }

?>


<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php the_title(); ?></h1>
</header>

<main class="row" role="main">
	<section class="col-md-12">
		<?php echo hce_form($form_step); ?>
	</section>
</main>

<?php get_footer(); ?>
