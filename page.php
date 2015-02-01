<?php get_header();

if ( have_posts() ) { while ( have_posts() ) : the_post();
?>

<header class="row" role="banner">
	<h1 class="col-md-12 page-header"><?php the_title(); ?></h1>
</header>

<main class="row" role="main">
	<section id="page-content" class="col-md-12">
		<?php the_content(); ?>
	</section>
</main>

<?php endwhile;
} // end if have_posts
get_footer(); ?>
