<?php
/* Template name: Formulario HCE */
get_header();

// call carbon footprint form
$form_out = hce_form();

?>

<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php the_title(); ?></h1>
</header>

<main class="row" role="main">
	<section class="col-md-12">
		<?php echo $form_out; ?>
	</section>
</main>

<?php get_footer(); ?>
