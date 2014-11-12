<?php
/* Template name: Formulario HCE */
get_header();

// form step
if ( array_key_exists('step', $_GET) ) { $step = sanitize_text_field($_GET['step']); }
else { $step = 1; }
if ( array_key_exists('project_id', $_GET) ) { $project_id = sanitize_text_field($_GET['project_id']); }
else { $project_id = 0; }

// form out
if ( is_user_logged_in() ) { // if user is logged in, then hce form
	$form_out = hce_form($step,$project_id);

} else { // if user in not logged in, then log in form
	$args = array(
		'echo' => false,
	);
	$form_out = wp_login_form( $args );
}

// actions depending on step
if ( $step == 2 && array_key_exists('hce-form-step-submit',$_POST) ) {
	// insert project basic data
	$field_prefix = "hce-form-step1-";
	$location = get_permalink()."?step=".$step;
	$project_tit = sanitize_text_field($_POST[$field_prefix.'name']);
	$project_desc = sanitize_text_field($_POST[$field_prefix.'desc']);
	$field_names = array("address","city","state","cp","use","built-area","useful-area","adjusted-area","users","budget","energy-label","energy-consumption","co2-emission");
	foreach ( $field_names as $field_name ) {
		$field = sanitize_text_field($_POST[$field_prefix.$field_name]);
		if ( $field != '' ) { $project_fields[$field_name] = $field; }
	}
	hce_project_insert_basic_data($project_tit,$project_desc,$project_fields,$location,$project_id);
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
