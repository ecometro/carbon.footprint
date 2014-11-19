<?php
/* Template name: Formulario HCE */
get_header();

// form step
if ( array_key_exists('step', $_GET) ) { $step = sanitize_text_field($_GET['step']); }
else { $step = 1; }

// form out
if ( is_user_logged_in() ) { // if user is logged in, then hce form
	// display carbon footprint form
	$form_out = hce_form();

	// actions depending on step
	if ( $step == 2 && array_key_exists('hce-form-step-submit',$_POST) ) {
		// insert project basic data
		hce_project_insert_basic_data();

	} // end step 2 actions
	elseif ( $step == 3 && array_key_exists('hce-form-step-submit',$_POST ) ) {
		// upload project file
		hce_project_upload_file();
	} // end step 3 actions

} else { // if user in not logged in, then log in form
	// display login form
	$args = array(
		'echo' => false,
	);
	$form_out = wp_login_form( $args );
}
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
