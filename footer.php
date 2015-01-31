</div><!-- .container -->
<footer id="epi" class="navbar navbar-default navbar-fixed-bottom hidden-print">
	<div class="container">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#footer-navbar-collapse">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
		</button>
		<div class="collapse navbar-collapse" id="footer-navbar-collapse">
				<ul class="epi-content list-inline pull-left">
					<li class="text-muted"><strong><small>Una iniciativa de</small></strong><br /><a href="http://www.sostenibilidadyarquitectura.com/"><img src="<?php echo HCE_BLOGTHEME."/images/logo.asa.png" ?>" alt="Logo de la Asociación Sostenibilidad y Arquitectura" /></a></li>
					<li class="text-muted"><strong><small>Con el apoyo de</small></strong><br /><a href="http://fundacion-biodiversidad.es/"><img src="<?php echo HCE_BLOGTHEME."/images/logo.f.biodiversidad.png" ?>" alt="Logo de la Fundación Biodiversidad" /></a></li>
				</ul>
	<?php $location = "footer-menu";
	if ( has_nav_menu( $location ) ) {
		$args = array(
			'theme_location'  => $location,
			'container' => false,
			'menu_id' => 'epi-menu',
			'menu_class' => 'nav navbar-nav pull-right'
		);
		wp_nav_menu( $args );
	} ?>
</div>
	</div>
</footer>
<?php
// get number of queries
//echo "<div style='display: none;'>".get_num_queries()."</div>";
wp_footer(); ?>

</body><!-- end body as main container -->
</html>
