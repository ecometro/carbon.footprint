<?php get_header();
if ( is_user_logged_in() && is_author( get_current_user_id() ) ) {
	$page_title = "Mis proyectos";
	$my_projects = 1;

} else {
	$page_title = "Proyectos de ".get_query_var('author_name');
	$my_projects = 0;

}
?>

<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php echo $page_title ?></h1>
</header>

<main class="row" role="main">
	<section class="col-md-9 col-sm-offset-3">
	<?php 
	if ( have_posts() ) {
		while ( have_posts() ) : the_post();
			include "loop-project.php";
		endwhile;

	} else {
		echo "<p>".__('No content.','montera34'). "</p>";
	}
	?>
	</section>
</main>
<?php get_footer(); ?>
