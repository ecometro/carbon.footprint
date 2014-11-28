<?php get_header();

if ( have_posts() ) { while ( have_posts() ) : the_post();
	$project_id = $post->ID;

	// visibility switcher
	$visibility_switcher_out = hce_project_visibility_switcher();

	// edit project link
	if ( is_user_logged_in() && get_current_user_id() == $post->post_author ) {
		$edit_link_out = "<p><a class='btn btn-default btn-xs' href='/calculo-huella-carbono/?step=1&project_id=".$post->ID."'>Editar proyecto</a></p>";
	} else { $edit_link_out = ""; }

	// get project basic data
	$basic_fields_out = hce_project_display_basic_data($project_id);

	// calculate emissions
	$table_p = $wpdb->prefix . "hce_project_" .$project_id;
	$sql_query = "
	SELECT
	  material_name,
	  section_name,
	  emission,
	  emission_transport
	FROM $table_p
	WHERE emission != 0
	  OR emission_transport != 0
	";
	$query_results = $wpdb->get_results( $sql_query , ARRAY_A );
//echo "<pre>";
//print_r($query_results);
//echo "</pre>";
	$emissions = array();
	$emissions_total = 0;
	foreach ( $query_results as $material ) {
		if ( !array_key_exists($material['section_name'],$emissions) ) {
			$emissions[$material['section_name']]['emission'] = $material['emission'];
			$emissions[$material['section_name']]['emission_transport'] = $material['emission_transport'];
		} else {
			$emissions[$material['section_name']]['emission'] += $material['emission'];
			$emissions[$material['section_name']]['emission_transport'] += $material['emission_transport'];
		}
		$emissions_total += $material['emission'] + $material['emission_transport'];
	}
	// total emission
//echo "<pre>";
//print_r($emissions);
//echo "</pre>";
//echo $emissions_total;
	$emission_per_section_out = "
	<div class='dossier-table-header row'>
		<div class='col-sm-4'><small>CAPÍTULO</small><div class='pull-right'><small>kg CO2 eq</small></div></div>
		<div class='col-sm-8'><span class='btn btn-primary btn-xs' disabled='disabled'>Intrínsecas</span> <span class='btn btn-info btn-xs' disabled='disabled'>Transporte</span></div>
	</div>
	";
	$e_max = 600000;
	$e_min = $e_max/100;
	foreach ( $emissions as $section => $e ) {
		$intrinsic = round( $e['emission'], 1 );
		$transport = round( $e['emission_transport'], 1 );
		$total = $intrinsic+$transport;
		if ( $intrinsic <= $e_min && $intrinsic != 0 ) { $intrinsic_relative = 5; }
		else { $intrinsic_relative = round( $intrinsic * 100 / $e_max ); }
		if ( $transport <= $e_min && $transport != 0 ) { $transport_relative = 10; }
		else { $transport_relative = round( $transport * 100 / $e_max ); }
		$emission_per_section_out .= "
		<div class='row'>
			<div class='col-sm-3'><small>".$section."</small></div>
			<div class='col-sm-1'><div class='pull-right'><small>".$total."</small></div></div>
			<div class='col-sm-8'>
				<div class='progress'>
					<div class='progress-bar' style='width: ".$intrinsic_relative."%;'>
					".$intrinsic."
					</div>
					<div class='progress-bar progress-bar-info' style='width: ".$transport_relative."%;'>
					".$transport."
					</div>

				</div>
			</div>
		</div>
		";
	}

?>

<header class="row" role="banner">
	<h1 class="col-sm-12 page-header"><?php the_title(); ?> <small>Memoria de Cálculo de Huella de Carbono</small></h1>
</header>

<main class="row" role="main">
	<div id="dossier-meta" class="col-sm-2">
		<?php echo $edit_link_out.$visibility_switcher_out; ?>
	</div>
	<div id="dossier-data" class="col-sm-10">
		<section>
			<header><h2 class="dossier-section-header">Datos del proyecto</h2></header>
			<?php echo $basic_fields_out ?>
		</section>
		<section>
			<header><h2>Emisiones</h2></header>
			<?php echo $emission_per_section_out ?>
		</section>
</main>

<?php endwhile;
} // end if have_posts
get_footer(); ?>
