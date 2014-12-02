<?php get_header();
if ( is_user_logged_in() && is_author( get_current_user_id() ) ) {
	$page_title = "Mis proyectos";
	$what_projects = 'mine';
	$page_error = "Esta es tu página de usuario, donde aparecerán los proyectos que vayas evaluando. Todavía no has evaluado ninguno.";

} elseif ( is_author() ) {
	$page_title = "Proyectos de ".get_query_var('author_name');
	$what_projects = 'author';
	$page_error = "Este usuario no ha hecho público ninguno de sus proyectos evaluados.";

} else {
	$page_title = "Proyectos evaluados";
	$what_projects = 'public';
	$page_error = "Aún no hay ningún proyecto evaluado publicado.";

}
?>
<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php echo $page_title ?></h1>
</header>

<main class="row" role="main">
	<section class="col-md-8 col-sm-offset-2">
	<?php 
	if ( have_posts() ) {
		while ( have_posts() ) : the_post();
			include "loop-project.php";
		endwhile;

	} else {
		echo "<p>".$page_error."</p>";
	}
	?>
	</section>
</main>
<?php get_footer(); ?>
