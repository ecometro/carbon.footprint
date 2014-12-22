<footer id="epi" class="row hidden-print">
	<div class="epi-content col-md-12">
		<ul class="list-inline">
		<li class="text-muted"><strong>Una iniciativa de</strong><br /><a href="http://www.sostenibilidadyarquitectura.com/"><img src="<?php echo HCE_BLOGTHEME."/images/logo.asa.png" ?>" alt="Logo de la Asociación Sostenibilidad y Arquitectura" /></a></li>
		<li class="text-muted"><strong>Con el apoyo de</strong><br /><a href="http://fundacion-biodiversidad.es/"><img src="<?php echo HCE_BLOGTHEME."/images/logo.f.biodiversidad.png" ?>" alt="Logo de la Fundación Biodiversidad" /></a></li>
		<li class="text-muted"><strong>Y la colaboración de</strong><br />
		<a href="http://www.ietcc.csic.es"><img src="<?php echo HCE_BLOGTHEME."/images/logo.i.torroja.png" ?>" alt="Logo del Instituto Eduardo Torroja" /></a>
		<a href="http://www.magrama.gob.es/es/cambio-climatico/temas/organismos-e-instituciones-implicados-en-la-lucha-contra-el-cambio-climatico-a-nivel-nacional/oficina-espanola-en-cambio-climatico/"><img src="<?php echo HCE_BLOGTHEME."/images/logo.oecc.png" ?>" alt="Logo de la Oficina Española del Cambio Climático" /></a></li>
		</ul>
	</div>
	<div class="col-md-12">
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
