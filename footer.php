<footer id="epi" class="row">
	<div class="epi-content col-md-12">
	<?php $location = "footer-menu";
	if ( has_nav_menu( $location ) ) {
		$args = array(
			'theme_location'  => $location,
			'container' => false,
			'menu_id' => 'epi-menu',
			'menu_class' => 'list-inline'
		);
		wp_nav_menu( $args );
	} ?>
		<p class="text-muted">Desarrollado por <a href="http://montera34.com">m34</a> usando <a href="#">software libre</a>.</p>
	</div>
</footer>
</div><!-- .container -->
<?php
// get number of queries
//echo "<div style='display: none;'>".get_num_queries()."</div>";
wp_footer(); ?>

</body><!-- end body as main container -->
</html>
