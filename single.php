<?php get_header();

if ( have_posts() ) { while ( have_posts() ) : the_post();
	$project_id = $post->ID;
	$project_name = get_the_title();

	// user data
	$author_id = get_the_author_meta( 'ID' );
	$username = get_the_author_meta( 'user_login' );
	$office = get_the_author_meta( 'first_name' );
	$web = get_the_author_meta( 'user_url' );
//	$author_bio = get_the_author_meta( 'description' );
	$author_projects_url = get_author_posts_url($author_id);
	if ( $office != '' && $web != '' ) { $office_out = "<dt>Equipo</dt><dd><a href='".$web."'>".$office."</a></dd>"; }
	elseif ( $office != '' && $web == '' ) { $office_out = "<dt>Equipo</dt><dd>".$office."</dd>"; }
	elseif ( $office == '' && $web != '' ) { $office_out = "<dt>Web</dt><dd><a href='".$web."'>".$web."</a></dd>"; }
	else { $office_out = ""; }
	$author_out = "
	<div id='dossier-author'>
	<dl>
		<dt>Evaluado por</dt><dd>".$username."</dd>
		".$office_out."
	</dl>
	<p class='hidden-print'><a class='btn btn-xs btn-default' href='".$author_projects_url."'><span class='glyphicon glyphicon-plus'></span> Proyectos de ".$username."</a></p>
	</div>
	";

	// visibility switcher
	$visibility_switcher_out = hce_project_visibility_switcher();

	// edit project link
	if ( is_user_logged_in() && get_current_user_id() == $post->post_author ) {
		$edit_link_out = "<p class='hidden-print'><a class='btn btn-default btn-xs' href='/calculo-huella-carbono/?step=1&project_id=".$post->ID."'><span class='glyphicon glyphicon-pencil'></span> Editar proyecto</a></p>";
	} else { $edit_link_out = ""; }

	// views switcher and print button
	if ( array_key_exists('view',$_GET) ) { $view = sanitize_text_field($_GET['view']); } else { $view = ""; }
	if ( is_user_logged_in() && get_current_user_id() == $post->post_author && $post->post_status != 'draft' ) {
		if ( $view == 'dossier' ) {
			$dossier_current = "<span class='btn btn-xs btn-info' disabled='disabled'>Vista informe</span>";
			$dossier_link_text = "<span class='glyphicon glyphicon-align-left'></span> Cambiar a vista resumen";
			$dossier_link = "results";
			$dossier_print = "<p><a class='btn btn-default btn-xs' href='javascript:if(window.print)window.print()'><span class='glyphicon glyphicon-print'></span> Imprimir informe</a></p>";
		} else {
			$dossier_current = "<span class='btn btn-xs btn-info' disabled='disabled'>Vista resumen</span>";
			$dossier_link_text = "<span class='glyphicon glyphicon-file'></span> Cambiar a vista informe";
			$dossier_link = "dossier";
			$dossier_print = "";
		}
		$dossier_link_out = "
		<div id='dossier-view' class='hidden-print'>
			<p>".$dossier_current."</p>
			<p><a class='btn btn-default btn-xs' href='?view=".$dossier_link."'>".$dossier_link_text."</a></p>"
			.$dossier_print.
		"</div>";

	} else { $dossier_link_out = ""; }

	// get project basic data
	$basic_fields_out = hce_project_display_basic_data($project_id);

	// get project transport data
	$transport_data = hce_project_display_transport_data($project_id);
	$transport_out = "
		<table class='table table-condensed'>
		<thead><tr>
			<th>Material</th>
			<th>Distancia</th>
			<th>Medio</th>
		</tr></thead>
		<tbody>		
	";
	foreach ( $transport_data as $mat ) {
		$transport_out .= "<tr><td>".$mat['material']."</td><td>".$mat['distance']."</td><td>".$mat['type']."</td></tr>";
	}
	$transport_out .= "</tbody></table>";

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
	ORDER BY section_code
	";
	$query_results = $wpdb->get_results( $sql_query , ARRAY_A );
	$emissions = array();
	// $emissions_total = ;
	$e_max = 0;
	foreach ( $query_results as $material ) {
		if ( !array_key_exists($material['section_name'],$emissions) ) {
			$emissions[$material['section_name']]['emission'] = $material['emission'];
			$emissions[$material['section_name']]['emission_transport'] = $material['emission_transport'];
		} else {
			$emissions[$material['section_name']]['emission'] += $material['emission'];
			$emissions[$material['section_name']]['emission_transport'] += $material['emission_transport'];
		}
		// search for highest emission
		$max_candidate = $emissions[$material['section_name']]['emission'] + $emissions[$material['section_name']]['emission_transport'];
		if ( $e_max <= $max_candidate ) { $e_max = $max_candidate; }
		// total emission
//		$emissions_total += $material['emission'] + $material['emission_transport'];
	}
	$emission_per_section_out = "
	<div class='dossier-table-header row'>
		<div class='col-sm-12'>
			<div><strong>EMISIONES POR CAPÍTULO</strong></div>
			<small>en kg CO2 eq</small>
			<span class='btn btn-info btn-xs' disabled='disabled'>Transporte</span> <span class='btn btn-warning btn-xs' disabled='disabled'>Embebidas</span>
		</div>
	</div>
	";
	$e_max = $e_max + $e_max * 0.05;
	$e_min = $e_max/100;
	foreach ( $emissions as $section => $e ) {
		$intrinsic = round( $e['emission'], 1 );
		$transport = round( $e['emission_transport'], 1 );
		$total = $intrinsic+$transport;
		if ( $intrinsic <= $e_min && $intrinsic != 0 ) { $intrinsic_relative = 0.5; }
		else { $intrinsic_relative = round( $intrinsic * 100 / $e_max ); }
		if ( $transport <= $e_min && $transport != 0 ) { $transport_relative = 0.5; }
		else { $transport_relative = round( $transport * 100 / $e_max ); }
		$emission_per_section_out .= "
		<div class='row emission-cap'>
			<div class='col-sm-12'>
				<div class='progress'>
					<div class='progress-bar progress-bar-info' style='width: ".$transport_relative."%;'>
					".$transport."
					</div>
					<div class='progress-bar progress-bar-warning' style='width: ".$intrinsic_relative."%;'>
					".$intrinsic."
					</div>
				</div>
				<div class='pull-right'><strong><small>".$section."</small></strong></div><small>".$transport." + ".$intrinsic." = <strong>".$total."</strong></small>
			</div>
		</div>
		";
	}

	// emissions circles
	$e_media_user = "15000";
	$e_media_m2 = "5200";
	$e_media_e = "200";
	$e_media_kg = "1.1";
	$cfield_prefix = '_hce_project_';
	$emissions_total = round( get_post_meta($post->ID,'_hce_project_emission_total',true) + get_post_meta($post->ID,'_hce_project_emission_transport_total',true) );
	$users = get_post_meta($project_id,$cfield_prefix."users",TRUE);
	$built_area = get_post_meta($project_id,$cfield_prefix."built-area",TRUE);
	$budget = get_post_meta($project_id,$cfield_prefix."budget",TRUE);
	$weight = get_post_meta($project_id,$cfield_prefix."mass_total",TRUE);
	$e_per_user = round($emissions_total/$users,2);
	$e_per_m2 = round($emissions_total/$built_area,2);
	$e_per_e = round($emissions_total/$budget,2);
	$e_per_kg = round($emissions_total/$weight,2);
	$circles = array(
		array($e_per_user,$e_media_user,"usuario","Usuarios: ".$users),
		array($e_per_m2,$e_media_m2,"m2 construido","Superficies construida: ".$built_area." m<sub>2</sub>"),
		array($e_per_e,$e_media_e,"euro gastado","Presupuesto: ".$budget." €"),
		array($e_per_kg,$e_media_kg,"kg de edificio","Peso: ".$weight." kg")
	);

	$circles_out = "<div class='row'>";
	$c_count = 0;
	foreach ( $circles as $c ) {
		$r_project = sqrt( $c[0]/M_PI );
		$r_media = sqrt( $c[1]/M_PI );
		$c_project = 50;
		$c_media = $r_media * 50 / $r_project;
		$c_media_zindex = 3;
		$c_project_zindex = 2;
		if ( $c_media > 100 ) {
			$c_media = 100;
			$c_project = $r_project * 100 / $r_media;
		}
		if ( $c_media >= $c_project ) { $c_project_zindex = 4; }
		$c_project_margin = ( 100 - $c_project ) / 2;
		$c_media_margin = ( 100 - $c_media ) / 2;
		$circles_out .= "
		<div class='col-sm-3 col-xs-6'>
			<div class='dossier-circle-pre'><strong><small>Por ".$c[2]."</small></strong></div>
			<div class='dossier-circles'>
				<div class='dossier-circle dossier-circle-media' style='width: ".$c_media."%; margin-top: ".$c_media_margin."%; z-index: ".$c_media_zindex.";'>
					<div class='dossier-circle-label bg-default'>".$c[1]."</div>
				</div>
				<div class='dossier-circle dossier-circle-project' style='width: ".$c_project."%; margin-top: ".$c_project_margin."%; z-index: ".$c_project_zindex.";'>
					<div class='dossier-circle-label bg-primary'>".$c[0]."</div>
				</div>
			</div>
			<div class='dossier-circle-epi'><span class='btn btn-primary btn-xs' disabled='disabled'>" .$c[0]. "</span> <span class='btn btn-default btn-xs' disabled='disabled'>".$c[1]."</span></div>
		</div>
		";
	}
	$circles_out .= "</div>";
?>

<header id="dossier-tit" role="banner">
	<h1 class="col-sm-12 page-header"><?php echo $project_name; ?> <small>Memoria de Cálculo de Huella de Carbono</small></h1>
</header>

<main class="row" role="main">
	<div id="dossier-meta" class="col-sm-3 page-break">
		<?php echo $author_out.$dossier_link_out.$edit_link_out.$visibility_switcher_out; ?>
	</div>
	<div id="dossier-data" class="col-sm-9">
		<?php if ( $view == 'dossier' ) { include "project-dossier.php"; } ?>
		<section class="dossier-section page-break" id="dossier-data">
			<header><h2>Datos del proyecto</h2></header>
			<?php echo $basic_fields_out; ?>
		</section>
		<section class="dossier-section" id="dossier-transport">
			<header><h2>Datos de transporte</h2></header>
			<?php echo $transport_out; ?>
		</section>

		<section class="dossier-section" id="dossier-emission">
		<?php if ( $view != 'dossier' ) { ?>
			<header class="row"><h2 class="col-sm-12">Emisiones</h2></header>
			<div class='row'>
				<div class='col-sm-12'>
					<div><strong>TOTAL</strong></div>	
					<span class='btn btn-primary btn' disabled='disabled'><?php echo $emissions_total; ?></span> <small>kg CO<sub>2</sub> eq</small>
				</div>
			</div>
			<div class='dossier-table-header row'>
				<div class='col-sm-12'>
					<div><strong>RELATIVAS</strong></div>	
					<small>en kg CO<sub>2</sub> eq</small> <span class='btn btn-primary btn-xs' disabled='disabled'>Proyecto actual</span> <span class='btn btn-default btn-xs' disabled='disabled'>Media todos los proyectos</span>
				</div>
			</div>
			<div class='row'>
				<div class="col-sm-12"><?php echo $circles_out; ?></div>
			</div>

		<?php } else { ?>
			<header class="row"><h2 class="col-sm-12">Emisiones</h2></header>
			<div class="row">
				<div class="col-sm-12">
					<h3>Totales</h3>
					<p><strong><?php echo $emissions_total; ?></strong> kg de CO<sub>2</sub> equivalente</p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3>Relativas</h3>
					<?php echo $circles_out; ?>
				</div>
			</div>

		<?php } ?>
			<?php echo $emission_per_section_out ?>
		</section>
	</div>
</main>

<?php endwhile;
} // end if have_posts
get_footer(); ?>
