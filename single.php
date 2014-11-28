<?php get_header();

if ( have_posts() ) { while ( have_posts() ) : the_post();
	$visibility_switcher_out = hce_project_visibility_switcher();

	$project_id = $post->ID;
	// get project basic data
	$cfield_prefix = '_hce_project_';
	$cfields_basic = array("address","city","state","cp","use","built-area","useful-area","adjusted-area","users","budget","energy-label","energy-consumption","co2-emission");
	$value['name'] = get_the_title();
	foreach ( $cfields_basic as $field ) {
		$value[$field] = get_post_meta($project_id,$cfield_prefix.$field,TRUE);
	}
	$value['desc'] = get_the_content();
	$basic_fields = array(
		array(
			'label' => 'Nombre del proyecto',
			'unit' => '',
			'value' => $value['name']
		),
		array(
			'label' => 'Localización',
			'unit' => '',
			'value' => $value['address']. ", " .$value['city']. ". " .$value['cp']. " " .$value['state']
		),
		array(
			'label' => 'Uso',
			'unit' => '',
			'value' => $value['use']
		),
		array(
			'label' => 'Superficie construida',
			'unit' => 'm2',
			'value' => $value['built-area']
		),
		array(
			'label' => 'Superficie útil',
			'unit' => 'm2',
			'value' => $value['useful-area']
		),
		array(
			'label' => 'Superficie computable',
			'unit' => 'm2',
			'value' => $value['adjusted-area']
		),
		array(
			'label' => 'Número de usuarios',
			'unit' => '',
			'value' => $value['users']
		),
		array(
			'label' => 'Presupuesto',
			'unit' => '€',
			'value' => $value['budget']
		),
		array(
			'label' => 'Calificación energética',
			'unit' => '',
			'value' => $value['energy-label']
		),
		array(
			'label' => 'Consumo energético anual',
			'unit' => 'kWh/m2 año',
			'value' => $value['energy-consumption']
		),
		array(
			'label' => 'Emisión anual de CO2',
			'unit' => 'Kg CO2/m2 año',
			'value' => $value['co2-emission']
		),
		array(
			'label' => 'Descripción',
			'unit' => '',
			'value' => $value['desc']
		)
	);
	$basic_fields_out = "";
	foreach ( $basic_fields as $field ) {
		$basic_fields_out .= "<dt>".$field['label']."</dt><dd>".$field['value']." ".$field['unit']."</dd>";
	}

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
	// emission per building user
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
		<?php echo $visibility_switcher_out; ?>
	</div>
	<div id="dossier-data" class="col-sm-10">
		<section class="row">
			<header><h2>Datos del proyecto</h2></header>
			<dl><?php echo $basic_fields_out ?></dl>
		</section>
		<section class="row">
			<header><h2>Emisiones</h2></header>
			<?php echo $emission_per_section_out ?>
		</section>
</main>

<?php endwhile;
} // end if have_posts
get_footer(); ?>
