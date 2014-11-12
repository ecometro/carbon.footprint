<?php
// theme setup main function
add_action( 'after_setup_theme', 'hce_theme_setup' );
function hce_theme_setup() {

	// theme global vars
	if (!defined('HCE_BLOGNAME'))
	    define('HCE_BLOGNAME', get_bloginfo('name'));

	if (!defined('HCE_BLOGDESC'))
	    define('HCE_BLOGDESC', get_bloginfo('description','display'));

	if (!defined('HCE_BLOGURL'))
	    define('HCE_BLOGURL', get_bloginfo('url'));

	if (!defined('HCE_BLOGTHEME'))
	    define('HCE_BLOGTHEME', get_bloginfo('template_directory'));

	/* Set up media options: sizes, featured images... */
	add_action( 'init', 'hce_media_options' );

	/* Add your nav menus function to the 'init' action hook. */
	add_action( 'init', 'hce_register_menus' );

	/* Load JavaScript files on the 'wp_enqueue_scripts' action hook. */
	add_action( 'wp_enqueue_scripts', 'hce_load_scripts' );

	/* Load scripts for IE compatibility */
	add_action('wp_head','hce_ie_scripts');

	// Custom post types
	add_action( 'init', 'hce_create_post_type', 0 );

	// Extra meta boxes in editor
	//add_filter( 'cmb_meta_boxes', 'hce_metaboxes' );
	// Initialize the metabox class
	//add_action( 'init', 'hce_init_metaboxes', 9999 );

	// excerpt support in pages
	add_post_type_support( 'page', 'excerpt' );

	// remove unused items from dashboard
	add_action( 'admin_menu', 'hce_remove_dashboard_item' );

	// disable admin bar in front end
	add_filter('show_admin_bar', '__return_false');

} // end hce theme setup function

// set up media options
function hce_media_options() {
	/* Add theme support for post thumbnails (featured images). */
	add_theme_support( 'post-thumbnails', array( 'project') );
	set_post_thumbnail_size( 231, 0 ); // default Post Thumbnail dimensions
	/* set up image sizes*/
	update_option('thumbnail_size_w', 231);
	update_option('thumbnail_size_h', 0);
	update_option('medium_size_w', 474);
	update_option('medium_size_h', 0);
	update_option('large_size_w', 717);
	update_option('large_size_h', 0);
} // end set up media options

// register custom menus
function hce_register_menus() {
        if ( function_exists( 'register_nav_menus' ) ) {
                register_nav_menus(
                array(
                        'header-menu' => 'Menú de cabecera',
                        'footer-menu' => 'Menú del pie de página',
                )
                );
        }
} // end register custom menus

// load js scripts to avoid conflicts
function hce_load_scripts() {
	wp_register_style( 'bootstrap-css', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css' );
	wp_register_style( 'hce-css', get_stylesheet_uri(), array('bootstrap-css') );
	wp_enqueue_style('hce-css');
	wp_enqueue_script('jquery');
//	wp_enqueue_script(
//		'bootstrap-js',
//		get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js',
//		array( 'jquery' ),
//		'3.3.0',
//		FALSE
//	);

} // end load js scripts to avoid conflicts

// load scripts for IE compatibility
function hce_ie_scripts() {
	echo "
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
	<script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
	<![endif]-->
	";
}

// register post types
function hce_create_post_type() {
	// Documento custom post type
	register_post_type( 'project', array(
		'labels' => array(
			'name' => __( 'Projects' ),
			'singular_name' => __( 'Project' ),
			'add_new_item' => __( 'Add a project' ),
			'edit' => __( 'Edit' ),
			'edit_item' => __( 'Edit this project' ),
			'new_item' => __( 'New project' ),
			'view' => __( 'View project' ),
			'view_item' => __( 'View this project' ),
			'search_items' => __( 'Search project' ),
			'not_found' => __( 'No project found' ),
			'not_found_in_trash' => __( 'No projects in trash' ),
			'parent' => __( 'Parent' )
		),
		'has_archive' => true,
		'public' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'menu_position' => 5,
		//'menu_icon' => get_template_directory_uri() . '/images/icon-post.type-integrantes.png',
		'hierarchical' => false, // if true this post type will be as pages
		'query_var' => true,
		'supports' => array('title', 'editor','excerpt','author','thumbnail' ),
		'rewrite' => array('slug'=>'project','with_front'=>false),
		'can_export' => true,
		'_builtin' => false,
		'_edit_link' => 'post.php?post=%d',
	));
} // end register post types

// remove item from wordpress dashboard
function hce_remove_dashboard_item() {
	remove_menu_page('edit.php');	
}

// insert project basic data from form
function hce_project_insert_basic_data($title,$content,$cfields = array(),$location,$update_id = 0 ) {
	$cfield_prefix = '_hce_project_';
	$user_ID = get_current_user_id();

	if ( $user_ID != 0 ) { // if user is logged in
		$project = get_post($update_id);

		if ( $project->ID == $update_id && $project->post_author == $user_ID ) { // if project exists, then update it
			$args = array(
				'ID' => $update_id,
				'post_title' => $title,
				'post_content' => $content,
			);
			// update project
			$project_id = wp_update_post($args);

		} else { // if project does not exist, then create it
			$args = array(
				'post_type' => 'project',
				'post_status' => 'draft',
				'post_author' => $user_ID,
				'post_title' => $title,
				'post_content' => $content,
			);
			// insert project
			$project_id = wp_insert_post($args);
		} // end if project exists

			if ( $project_id != 0 ) { // if project has been created
				// insert custom fields
				reset($cfields);
				foreach ( $cfields as $key => $value ) {
					update_post_meta($project_id, $cfield_prefix.$key, $value);
				}
		$location .= "&project_id=".$project_id;
			} // end if project has been created

		// redirect to prevent resend
		wp_redirect( $location );
		exit;

	} // end if user is logged in


} // end insert project basic data from form

// display HCE form to evaluate a project
function hce_form($step,$project_id = 0 ) {

	$wrong_step = 4;
	$location = get_permalink();
	$user_ID = get_current_user_id();

	if ( $step >= $wrong_step ) {
		wp_redirect( $location );
		exit;
	}

	// prev and next steps links
	if ( $step == $wrong_step - 1 ) { $action_next = ""; } else {
		$next_step = $step + 1;
		$action_next = $location."?step=".$next_step;
		if ( $project_id != 0 ) { $action_next .= "&project_id=".$project_id; }
	}
	if ( $step != 1 ) {
		$prev_step = $step - 1;
		$action_prev = $location."?step=".$prev_step;
		if ( $project_id != 0 ) { $action_prev .= "&project_id=".$project_id; }
		$prev_step_out = "<span class='glyphicon glyphicon-chevron-left'></span> <a href='".$action_prev."' class='btn btn-default'>Paso ".$prev_step."</a>";
	} else { $action_prev = ""; $prev_step_out = ""; }

	// WHAT TO SHOW
	// in step 1
	if ( $step == 1 ) {
		$field_names = array("address","city","state","cp","use","built-area","useful-area","adjusted-area","users","budget","energy-label","energy-consumption","co2-emission");
		if ( $project_id != 0 ) { // if project_id is defined
			$project = get_post($project_id,ARRAY_A);
			if ( is_array($project) && $user_ID == $project['post_author'] ) { // if projects exists and user is the author
				$value['name'] = get_the_title($project_id);
				$value_desc = $project['post_content'];
				$cfield_prefix = '_hce_project_';
				foreach ( $field_names as $field_name ) {
					$value[$field_name] = get_post_meta($project_id,$cfield_prefix.$field_name,TRUE);
				}
			} else {
				wp_redirect( $location );
				exit;
			} // end if project exists and user is the author

		} else { // if project_id is not defined
			$value['name'] = '';
			$value_desc = '';
			foreach ( $field_names as $field_name ) {
				$value[$field_name] = '';
			}

		} // end if project_id is defined

		$enctype_out = "";
		// fields
		$fields = array(
			array(
				'label' => 'Nombre del proyecto',
				'name' => 'name',
				'required' => 1,
				'unit' => '',
				'comment' => '',
				'value' => $value['name']
			),
			array(
				'label' => 'Calle',
				'name' => 'address',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['address']
			),
			array(
				'label' => 'Localidad',
				'name' => 'city',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['city']
			),
			array(
				'label' => 'Provincia',
				'name' => 'state',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['state']
			),
			array(
				'label' => 'Código postal',
				'name' => 'cp',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['cp']
			),
			array(
				'label' => 'Uso',
				'name' => 'use',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['use']
			),
			array(
				'label' => 'Superficie construida',
				'name' => 'built-area',
				'required' => 1,
				'unit' => 'm2',
				'comment' => '',
				'value' => $value['built-area']
			),
			array(
				'label' => 'Superficie útil',
				'name' => 'useful-area',
				'required' => 1,
				'unit' => 'm2',
				'comment' => '',
				'value' => $value['useful-area']
			),
			array(
				'label' => 'Superficie computable',
				'name' => 'adjusted-area',
				'required' => 1,
				'unit' => 'm2',
				'comment' => '',
				'value' => $value['adjusted-area']
			),
			array(
				'label' => 'Número de usuarios',
				'name' => 'users',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['users']
			),
			array(
				'label' => 'Presupuesto',
				'name' => 'budget',
				'required' => 0,
				'unit' => '€',
				'comment' => '',
				'value' => $value['budget']
			),
			array(
				'label' => 'Calificación energética',
				'name' => 'energy-label',
				'required' => 0,
				'unit' => '',
				'comment' => '',
				'value' => $value['energy-label']
			),
			array(
				'label' => 'Consumo energético anual',
				'name' => 'energy-consumption',
				'required' => 0,
				'unit' => 'kWh/m2 año',
				'comment' => '',
				'value' => $value['energy-consumption']
			),
			array(
				'label' => 'Emisión anual de CO2',
				'name' => 'co2-emission',
				'required' => 0,
				'unit' => 'Kg CO2/m2 año',
				'comment' => '',
				'value' => $value['co2-emission']
			),
		);
	
		$fields_out = "";
		foreach ( $fields as $field ) {
			if ( $field['required'] == 1 ) { $req_class = " req"; } else { $req_class = ""; }
			if ( $field['unit'] != '' ) {
				$feedback_class = " has-feedback";
				$feedback = "<span class='form-control-feedback'>".$field['unit']."</span>";
			} else { $feedback_class = ""; $feedback = ""; }
			if ( $field['comment'] != '' ) {
	    			$help = "<p class='help-block col-sm-4'><small>".$field['comment']."</small></p>";
			} else { $help = ""; }
			$fields_out .= "
			<fieldset class='form-group".$feedback_class."'>
				<label for='hce-form-step".$step."-".$field['name']."' class='col-sm-3 control-label'>".$field['label']."</label>
				<div class='col-sm-5'>
					<input class='form-control".$req_class."' type='text' value='".$field['value']."' name='hce-form-step".$step."-".$field['name']."' />
					".$feedback."
				</div>
				".$help."
			</fieldset>
			";
		}
		$fields_out .= "
		<fieldset class='form-group'>
				<label for='hce-form-step".$step."-desc' class='col-sm-3 control-label'>Descripción del proyecto</label>
				<div class='col-sm-5'>
					<textarea class='form-control' rows='3' name='hce-form-step".$step."-desc'>".$value_desc."</textarea>
				</div>
		</fieldset>
		";

	}
	// in step 2
	elseif ( $step == 2 ) {
		$enctype_out = " enctype='multipart/form-data'";
		$fields_out = "
		<fieldset class='form-group'>
			<label for='hce-form-step".$step."-csv' class='col-sm-3 control-label'>Archivo presupuesto</label>
			<div class='col-sm-5'>
				<input type='file' name='hce-form-step".$step."-csv' />
				<input type='hidden' name='MAX_FILE_SIZE' value='4000000' />
			</div>
			<p class='col-sm-4 help-block'><small>Aquí algunas instrucciones que cuenten cosas...</small></p>
		</fieldset>
		";
	} // end if step 2
	// END WHAT TO SHOW

	// form output
	$form_out = "
	<form class='row' id='hce-form-step".$step."' method='post' action='" .$action_next. "'" .$enctype_out. ">
		<div class='form-horizontal col-md-12'>
		<fieldset class='form-group'>
			<div class='col-sm-offset-3 col-sm-5'>
				<button type='button' class='btn btn-default btn-lg' disabled='disabled'>Paso ".$step."</button>
			</div>
		</fieldset>
		".$fields_out."
		<fieldset class='form-group'>
			<div class='col-sm-offset-3 col-sm-5'>
				".$prev_step_out."
				<div class='pull-right'>
					<input class='btn btn-primary ' type='submit' value='Paso ".$next_step."' name='hce-form-step-submit' /><span class='glyphicon glyphicon-chevron-right'></span>
    				</div>
    			</div>
		</fieldset>
		</div>
	</form>
	";
	return $form_out;
} // end display HCE form to evaluate a project

?>
