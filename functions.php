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

	// create custom tables in DB
	add_action('after_switch_theme', 'hce_db_materials_table');
	add_action('after_switch_theme', 'hce_db_emissions_table');

	// update custom tables structure in DB
	add_action( 'init', 'hce_db_custom_tables_update', 99 );

	// populate emissions table
	add_action( 'init', 'hce_db_emissions_table_populate', 100 );

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
function hce_project_insert_basic_data() {

	$location = get_permalink();
	$user_ID = get_current_user_id();
	if ( $user_ID != 0 ) { // if user is logged in

		// check if update or new insert
		if ( array_key_exists('project_id',$_GET) ) {
			$update_id = sanitize_text_field($_GET['project_id']);
			$project = get_post($update_id);
			if ( $project->post_author != $user_ID ) { // if current user is not the author
				$location .= "?step=1&feedback=wrong_user";
				wp_redirect($location);
				exit;

			} elseif ( $project->ID != $update_id ) { // if project does not exist yet
				$update_id = 0;
			}
		}
		else { $update_id = 0; }

		$field_prefix = "hce-form-step1-";
		$title = sanitize_text_field($_POST[$field_prefix.'name']);
		$content = sanitize_text_field($_POST[$field_prefix.'desc']);
		$cfield_prefix = '_hce_project_';
		// check if required fields exist
		if ( $title == '' ) { // if project name is empty
			$location .= "?step=1&feedback=required_field";
			wp_redirect($location);
			exit;
		}
		$req_cfields = array("built-area","useful-area","adjusted-area","users","budget");
		foreach ( $req_cfields as $req_cfield ) {
			$field = sanitize_text_field($_POST[$field_prefix.$req_cfield]);
			if ( $field == '' ) { // if any custom field is empty
				$location .= "?step=1&feedback=required_field";
				wp_redirect($location);
				exit;
			} else { $cfields[$cfield_prefix.$req_cfield] = $field; }
		}
		// end check if required fields exist

		// no required fields
		$notreq_cfields = array("address","city","state","cp","use","energy-label","energy-consumption","co2-emission");
		foreach ( $notreq_cfields as $notreq_cfield ) {
			$field = sanitize_text_field($_POST[$field_prefix.$notreq_cfield]);
			if ( $field != '' ) { $cfields[$cfield_prefix.$notreq_cfield] = $field; }
		}
		// end no required fields

		if ( $update_id != 0 ) { // if project exists, then update it
			$args = array(
				'ID' => $update_id,
				'post_title' => $title,
				'post_content' => $content,
			);
			// update project
			$project_id = wp_update_post($args);
			$location_feedback = "&feedback=project_updated";

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
			$location_feedback = "&feedback=project_inserted";

		} // end if project exists

			if ( $project_id != 0 ) { // if project has been created
				// insert custom fields
				reset($cfields);
				foreach ( $cfields as $key => $value ) {
					update_post_meta($project_id, $key, $value);
				}
				// create custom table for project in DB
				hce_project_create_table($project_id);
				$location .= "?step=2&project_id=".$project_id.$location_feedback;
			} // end if project has been created

		// redirect to prevent resend
		wp_redirect( $location );
		exit;

	} else { // if user is not logged in
		$location .= "?step=1&feedback=user";
		wp_redirect($location);
		exit;

	} // end if user is logged in

} // end insert project basic data from form

// create project table in DB
function hce_project_create_table($project_id) {
	global $wpdb;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$table_name = $wpdb->prefix . "hce_project_" .$project_id; 

	$sql = "
	CREATE TABLE $table_name (
	  id bigint(20) unsigned NOT NULL auto_increment,
	  material_code varchar(12) NOT NULL default '',
	  material_name varchar(100) NOT NULL default '',
	  material_amount float(10,3) NOT NULL default 0,
	  material_unit varchar(10) NOT NULL default '',
	  construction_unit_code varchar(12) NOT NULL default '',
	  construction_unit_name varchar(100) NOT NULL default '',
	  construction_unit_amount float(10,3) NOT NULL default 0,
	  construction_unit_unit varchar(10) NOT NULL default '',
	  section_code varchar(12) NOT NULL default '',
	  section_name varchar(100) NOT NULL default '',
	  subsection_code varchar(12) NOT NULL default '',
	  subsection_name varchar(100) NOT NULL default '',
	  emission float(10,5) NOT NULL default 0,
	  PRIMARY KEY  (id)
	) $charset_collate;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

} // end create project table in DB

// populate project table in DB
function hce_project_populate_table($project_id,$csv_file_id) {

	global $wpdb;
	$cfield_prefix = '_hce_project_';
	$location = get_permalink();

	// data file
	$filename = wp_get_attachment_url($csv_file_id); // relative path to data filename
	$line_length = "4096"; // max line lengh (increase in case you have longer lines than 1024 characters)
	$delimiter = ";"; // field delimiter character
	$enclosure = ''; // field enclosure character
	
	// open the data file
	$fp = fopen($filename,'r');

	if ( $fp !== FALSE ) { // if the file exists and is readable
	
		$table = $wpdb->prefix . "hce_project_" .$project_id; 
		$format = array(
			//'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		);

		$line = 0;
		while ( ($fp_csv = fgetcsv($fp,$line_length,$delimiter)) !== FALSE ) { // begin main loop
			if ( $line == 0 ) { /* csv file headers */ }
			else {
				// check  empty lines
				if ( $fp_csv[0] == '' ) { /* empty or not valid line */ }
				else {
					// preparing data to insert
					$material_amount = str_replace(",",".",$fp_csv[3]);
					$material_amount = round($material_amount,3);
					$construction_unit_amount = str_replace(",",".",$fp_csv[7]);
					$construction_unit_amount = round($construction_unit_amount,3);
					$data = array(
						//'id' => is autoincrement
						'material_code' => $fp_csv[0],
						'material_name' => mb_convert_encoding($fp_csv[2], "UTF-8"),
						'material_amount' => $material_amount,
						'material_unit' => $fp_csv[1],
						'construction_unit_code' => $fp_csv[4],
						'construction_unit_name' => mb_convert_encoding($fp_csv[6], "UTF-8"),
						'construction_unit_amount' => $construction_unit_amount,
						'construction_unit_unit' => $fp_csv[5],
						'section_code' => $fp_csv[10],
						'section_name' => mb_convert_encoding($fp_csv[11], "UTF-8"),
						'subsection_code' => $fp_csv[8],
						'subsection_name' => mb_convert_encoding($fp_csv[9], "UTF-8")
					);
					/* create row */ $wpdb->insert( $table, $data, $format );
	
				} // end if valid line

			} // end if not line 0
			$line++;

		} // end main loop
		fclose($fp);

	} else { // if data file do not exist
		// if there was a problem with CSV file, we try to delete it
		if ( false === wp_delete_attachment( get_post_meta($project_id,$cfield_prefix.'csv_file',true), true ) ) {
		} else { delete_post_meta($project_id,$cfield_prefix.'csv_file'); }

		$location .= "?step=2&project_id=".$project_id."&feedback=populate_table";
		wp_redirect($location);
		exit;

	} // end if file exist and is readable

} // end populate project table in DB


// upload project file
function hce_project_upload_file() {
	$location = get_permalink();
	$cfield_prefix = '_hce_project_';
	$user_ID = get_current_user_id();

	if ( $user_ID != 0 ) { // if user is logged in

		if ( array_key_exists('project_id', $_GET) ) { $project_id = sanitize_text_field($_GET['project_id']); }
		else { $project_id = 0; }
		$project = get_post($project_id);
		if ( $project->post_author != $user_ID ) { // if current user is not the author
				$location .= "?step=1&feedback=wrong_user";
				wp_redirect($location);
				exit;

		} elseif ( $project->ID != $project_id ) { // if project does not exist yet
				$location .= "?step=1&feedback=project";
				wp_redirect($location);
				exit;
		}

		// if 'Delete file' button has been click, then delete file
		if ( sanitize_text_field($_POST['hce-form-step-submit']) == 'Sustituir archivo' ) {
			if ( false === wp_delete_attachment( get_post_meta($project_id,$cfield_prefix.'csv_file',true), true ) ) {
				$location .= "?step=2&project_id=".$project_id."&feedback=file_not_deleted";
			} else {
				delete_post_meta($project_id,$cfield_prefix.'csv_file');
				$location .= "?step=2&project_id=".$project_id."&feedback=file_deleted";
			}
			wp_redirect($location);
			exit;

		}
		// end delete file

		// check if file has been added with form
		if ( array_key_exists('hce-form-step2-csv', $_FILES) ) {
			$file = $_FILES['hce-form-step2-csv'];
			// recheck if file has been added with form
			if ( $file['name'] != '' ) {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime = $finfo->file($file['tmp_name']); 
				// checking if csv file has the right format and size
				if ( $mime == 'text/plain' || $mime == 'text/csv' ) { /* if is text plain file */ }
				else {
					$location .= "?step=2&project_id=".$project_id."&feedback=fileformat";
					wp_redirect($location);
					exit;
				}
				if ( $file['size'] >= '40000' ) {
					$location .= "?step=2&project_id=".$project_id."&feedback=filesize";
					wp_redirect($location);
					exit;
				}

			} else {
				// if filename is empty: no file uploaded
				$location .= "?step=2&project_id=".$project_id."&feedback=nofile";
				wp_redirect($location);
				exit;

			} // end recheck if file has been added with form

		} else {
			// if no file uploaded
			$location .= "?step=2&project_id=".$project_id."&feedback=nofile";
			wp_redirect($location);
			exit;

		} // end check if file has been added with form

		// file insert
		$upload_dir_vars = wp_upload_dir();
		$upload_dir = $upload_dir_vars['path']; // absolute path to uploads folder
		$uploaddir = realpath($upload_dir);

		$filename = basename($file['name']); // file name in client machine
		$filename = trim($filename); // removing spaces at the begining and end
		$filename = str_replace(" ", "-", $filename); // removing spaces inside the name

		$typefile = $file['type']; // file type
		$uploadfile = $uploaddir.'/'.$filename;

		$slugname = preg_replace('/\.[^.]+$/', '', basename($uploadfile));

		// if filename already associated to other file
		if ( file_exists($uploadfile) ) {
			$count = "a";
			while ( file_exists($uploadfile) ) {
				$count++;
				$uploadfile = $uploaddir.'/'.$slugname.'-'.$count.'.csv';
			}
		} // end if filename already associated to other file

		// if the file is correctly uploaded, then do the insert
		if ( move_uploaded_file($file['tmp_name'], $uploadfile) ) {
			$attachment = array(
				'post_mime_type' => $typefile,
				'post_title' => "Materiales del proyecto " .$project->post_title,
				'post_content' => '',
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $uploadfile, $project_id );

			if ( $attach_id != 0 ) { // if CSV has been inserted
				// you must first include the image.php file
				// for the function wp_generate_attachment_metadata() to work
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');

				$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadfile );
				wp_update_attachment_metadata( $attach_id,  $attach_data );
		
				update_post_meta($project_id, $cfield_prefix.'csv_file', $attach_id);
				//$img_url = wp_get_attachment_url($attach_id);

				// populate project table with file data
				hce_project_populate_table($project_id,$attach_id);

			} // end if CSV has been inserted

		} // end if the file is correctly uploaded

		// redirect to prevent resend
		$location .= "?step=3&project_id=".$project_id."&feedback=file_uploaded";
		wp_redirect( $location );
		exit;

	} else { // if user is not logged in
			$location .= "?step=1&feedback=user";
			wp_redirect($location);
			exit;

	} // end if user is logged in

} // end upload project file


// display HCE form to evaluate a project
function hce_form() {

	$last_step = 3;
	$location = get_permalink();
	$user_ID = get_current_user_id();

	// form step and current project id
	if ( array_key_exists('step', $_GET) ) { $step = sanitize_text_field($_GET['step']); }
	else { $step = 1; }
	if ( array_key_exists('project_id', $_GET) ) {
		$project_id = sanitize_text_field($_GET['project_id']);
		$project = get_post($project_id,ARRAY_A);
		if ( $project['ID'] != $project_id ) { // if project does not exists
			$location .= "?step=1&feedback=project";
			wp_redirect( $location );
			exit;
		}
		elseif ( $user_ID != $project['post_author'] ) { // if user is not the author
			$location .= "?step=1&feedback=wrong_user";
			wp_redirect( $location );
			exit;
		}

	} else { $project_id = 0; }
	// form feedback
	if ( array_key_exists('feedback',$_GET) ) { $form_feedback = sanitize_text_field($_GET['feedback']); }
	else { $form_feedback = '';  }

	if ( $step >> $last_step ) {
		wp_redirect( $location );
		exit;
	}

	// prev and next steps links
	if ( $step == $last_step ) { $action_next = ""; } else {
		$next_step = $step + 1;
		$action_next = $location."?step=".$next_step;
		if ( $project_id != 0 ) { $action_next .= "&project_id=".$project_id; }
	}
	if ( $step != 1 ) {
		$prev_step = $step - 1;
		$action_prev = $location."?step=".$prev_step;
		if ( $project_id != 0 ) { $action_prev .= "&project_id=".$project_id; }
		$prev_step_out = "<span class='glyphicon glyphicon-chevron-left'></span> <a href='".$action_prev."' class='btn btn-default'>Volver al paso ".$prev_step."</a>";
	} else { $action_prev = ""; $prev_step_out = ""; }

	// FORM FEEDBACK: error and success
	if ( $form_feedback != '' ) {
		if ( $form_feedback == 'required_field' ) { $feedback_type = "danger"; $feedback_text = "Alguno de los campos requeridos para enviar el formulario están vacíos. Por favor, revisalos y vuelve a intentarlo."; }
		elseif ( $form_feedback == 'fileformat' ) { $feedback_type = "danger"; $feedback_text = "El archivo debe ser CSV. Parece que el que has intentado subir no lo es. Puedes intentarlo de nuevo en el formulario de abajo."; }
		elseif ( $form_feedback == 'filesize' ) { $feedback_type = "danger"; $feedback_text = "El archivo debe ser menor de 40kB. Parece que el que has intentado subir pesa más. Puedes intentarlo de nuevo en el formulario de abajo."; }
		elseif ( $form_feedback == 'nofile' ) { $feedback_type = "danger"; $feedback_text = "No has añadido ningún archivo. Sin archivo no puedes continuar con el proceso de evaluación de tu proyecto."; }
		elseif ( $form_feedback == 'project' ) { $feedback_type = "danger"; $feedback_text = "Algo no encaja: el proyecto que intentas evaluar parece que no existe. Vuelve a empezar."; }
		elseif ( $form_feedback == 'wrong_user' ) { $feedback_type = "danger"; $feedback_text = "Algo no encaja: parece que tú no eres el autor del proyecto que intentas editar. Vuelve a empezar."; }
		elseif ( $form_feedback == 'file_not_deleted' ) { $feedback_type = "danger"; $feedback_text = "Algo falló: el archivo de mediciones no se ha eliminado. Quizás quieras volver a intentarlo."; }
		elseif ( $form_feedback == 'populate_table' ) { $feedback_type = "danger"; $feedback_text = "La información del archivo de mediciones no pudo incorporarse a la base de datos. Revisa la estructura del archivo e intenta volver a subirlo al servidor."; }
		elseif ( $form_feedback == 'user' ) { $feedback_type = "success"; $feedback_text = "Debes iniciar sesión para evaluar un proyecto."; }
		elseif ( $form_feedback == 'project_inserted' ) { $feedback_type = "success"; $feedback_text = "Los datos del proyecto han sido guardados."; }
		elseif ( $form_feedback == 'project_updated' ) { $feedback_type = "success"; $feedback_text = "Los datos del proyecto han sido actualizados."; }
		elseif ( $form_feedback == 'file_uploaded' ) { $feedback_type = "success"; $feedback_text = "El archivo de mediciones ha sido guardado correctamente."; }
		elseif ( $form_feedback == 'file_deleted' ) { $feedback_type = "success"; $feedback_text = "El archivo de mediciones ha sido eliminado. Ahora puedes añadir uno nuevo."; }
		$feedback_out = "<div class='alert alert-".$feedback_type."' role='alert'>".$feedback_text."</div>";
	} else { $feedback_out = ""; }
	// end ERROR FEEDBACK

	// WHAT TO SHOW
	// in step 1
	if ( $step == 1 ) {
		$field_names = array("address","city","state","cp","use","built-area","useful-area","adjusted-area","users","budget","energy-label","energy-consumption","co2-emission");
		if ( $project_id != 0 ) { // if project_id is defined
			$value['name'] = get_the_title($project_id);
			$value_desc = $project['post_content'];
			$cfield_prefix = '_hce_project_';
			foreach ( $field_names as $field_name ) {
				$value[$field_name] = get_post_meta($project_id,$cfield_prefix.$field_name,TRUE);
			}

		} else { // if project_id is not defined
			$value['name'] = '';
			$value_desc = '';
			foreach ( $field_names as $field_name ) {
				$value[$field_name] = '';
			}

		} // end if project_id is defined

		$enctype_out = "";
		$submit_out = 'Guardar e ir al paso '.$next_step;
		$next_step_out = "<input class='btn btn-primary ' type='submit' value='".$submit_out."' name='hce-form-step-submit' /> <span class='glyphicon glyphicon-chevron-right'></span>";
		// fields
		$fields = array(
			array(
				'label' => 'Nombre del proyecto',
				'name' => 'name',
				'required' => 1,
				'unit' => '',
				'comment' => '<span class="glyphicon glyphicon-asterisk"></span> Campos requeridos.',
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
				'required' => 1,
				'unit' => '',
				'comment' => '',
				'value' => $value['users']
			),
			array(
				'label' => 'Presupuesto',
				'name' => 'budget',
				'required' => 1,
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
			if ( $field['required'] == 1 ) { $req_class = " <span class='glyphicon glyphicon-asterisk'></span>"; } else { $req_class = ""; }
			if ( $field['unit'] != '' ) {
				$feedback_class = " has-feedback";
				$feedback = "<span class='form-control-feedback'>".$field['unit']."</span>";
			} else { $feedback_class = ""; $feedback = ""; }
			if ( $field['comment'] != '' ) {
	    			$help = "<p class='help-block col-sm-4'><small>".$field['comment']."</small></p>";
			} else { $help = ""; }
			$fields_out .= "
			<fieldset class='form-group".$feedback_class."'>
				<label for='hce-form-step".$step."-".$field['name']."' class='col-sm-3 control-label'>".$field['label'].$req_class."</label>
				<div class='col-sm-5'>
					<input class='form-control' type='text' value='".$field['value']."' name='hce-form-step".$step."-".$field['name']."' />
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
		// check if project has already a csv file uploaded
		$csv_file_id = get_post_meta($project_id,'_hce_project_csv_file',true);
		if ( $csv_file_id != '' ) {
			$csv_file = get_post($csv_file_id);
			$enctype_out = "";
			$link_out = 'Ir al paso '.$next_step;
			$submit_out = 'Sustituir archivo';
			$next_step_out = "<input class='btn btn-danger' type='submit' value='".$submit_out."' name='hce-form-step-submit' /> <span class='glyphicon glyphicon-warning-sign'></span> <a class='second-submit-button btn btn-primary' href='".$location."?step=".$next_step."&project_id=".$project_id."'>".$link_out."</a> <span class='glyphicon glyphicon-chevron-right'></span>";
			$fields_out = "
			<fieldset class='form-group'>
				<label for='hce-form-step".$step."-csv' class='col-sm-3 control-label'>Archivo de mediciones</label>
				<div class='col-sm-5'>
					<p>El archivo de mediciones asociado a este proyecto fue correctamente añadido y procesado.</p>
				</div>
			</fieldset>
			";		

		} else {
			$enctype_out = " enctype='multipart/form-data'";
			$submit_out = 'Subir archivo e ir al paso '.$next_step;
			$next_step_out = "<input class='btn btn-primary ' type='submit' value='".$submit_out."' name='hce-form-step-submit' /> <span class='glyphicon glyphicon-chevron-right'></span>";
			$fields_out = "
			<fieldset class='form-group'>
				<label for='hce-form-step".$step."-csv' class='col-sm-3 control-label'>Archivo de mediciones</label>
				<div class='col-sm-5'>
					<input type='file' name='hce-form-step".$step."-csv' />
					<input type='hidden' name='MAX_FILE_SIZE' value='40000' />
				</div>
				<p class='col-sm-4 help-block'><small>Formato CSV. Tamaño máximo 40kB.</small></p>
			</fieldset>
			";

		}

	}
	// in step 3
	elseif ( $step == 3 ) {
		$enctype_out = "";
		$submit_out = "Calcular emisiones";
		$next_step_out = "<input class='btn btn-primary ' type='submit' value='".$submit_out."' name='hce-form-step-submit' /> <span class='glyphicon glyphicon-chevron-right'></span>";
		$distances_out = "
			<option value=''>Distancia</option>
			<option value='200'>Local (200 km)</option>
			<option value='800'>Nacional (800 km)</option>
			<option value='2500'>Europea (2500 km)</option>
			<option value='8000'>Internacional (8000 km)</option>
		"; 
		$types_out = "
			<option value=''>Medio</option>
			<option value=''>Barco de carga</option>
			<option value=''>Tren de carga</option>
			<option value=''>Transporte por carretera</option>
		";
		$fields_out = "
		<fieldset class='form-group'>
			<label for='hce-form-step".$step."-desc' class='col-sm-3 control-label'>Tipo material</label>
			<div class='col-sm-2'>
				<select class='form-control' name='hce-form-step".$step."-transport-distance'>".$distances_out."</select>
			</div>
			<div class='col-sm-3'>
				<select class='form-control' name='hce-form-step".$step."-transport-type'>".$types_out."</select>
			</div>
		</fieldset>
		";
	}
	// END WHAT TO SHOW

	// steps nav menu
	$btns = array(
		array(
			'step' => 1,
			'status' => " btn-default",
			'text' => "Proyecto",
			'after' => " <span class='glyphicon glyphicon-chevron-right'></span> "
		),
		array(
			'step' => 2,
			'status' => " btn-default",
			'text' => "Materiales",
			'after' => " <span class='glyphicon glyphicon-chevron-right'></span> "
		),
		array(
			'step' => 3,
			'status' => " btn-default",
			'text' => "Transporte",
			'after' => ""
		),
	);
	$nav_btns_out = "<strong>Pasos:</strong> ";
	reset($btns);
	foreach ( $btns as $btn ) {
		if ( $step == $btn['step'] ) { $btn['status'] = " btn-primary"; }
		$nav_btns_out .= "<button type='button' class='btn btn-sm".$btn['status']."' disabled='disabled'>".$btn['step'].". ".$btn['text']."</button>".$btn['after'];	
	}

	// form output
	$form_out = "
	<div class='row'>
		<div id='form-steps' class='col-sm-5'>".$nav_btns_out."</div>
		<div class='col-sm-3'>".$feedback_out."</div>
	</div>

	<form class='row' id='hce-form-step".$step."' method='post' action='" .$action_next. "'" .$enctype_out. ">
		<div class='form-horizontal col-md-12'>
		".$fields_out."
		<fieldset class='form-group'>
			<div class='col-sm-offset-3 col-sm-5'>
				".$prev_step_out."
				<div class='pull-right'>".$next_step_out."</div>
    			</div>
		</fieldset>
		</div>
	</form>
	";
	return $form_out;
} // end display HCE form to evaluate a project

// create or update emissions table in DB
global $emissions_ver;
$emissions_ver = "0.1"; 
function hce_db_emissions_table() {
	global $wpdb;
	global $emissions_ver;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$table_name = $wpdb->prefix . "hce_emissions"; 

	$sql = "
	CREATE TABLE $table_name (
	  id bigint(20) unsigned NOT NULL auto_increment,
	  opendap_code char(7) NOT NULL default '0000000',
	  type varchar(200) NOT NULL default '',
	  subtype varchar(200) NOT NULL default '',
	  emission_factor float(10,10) NOT NULL default 0,
	  PRIMARY KEY  (id)
	) $charset_collate;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'hce_emissions_version', $emissions_ver );

} // end create emissions table in DB

// create materials table in DB
global $materials_ver;
$materials_ver = "0.1";
function hce_db_materials_table() {
	global $wpdb;
	global $materials_ver;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$table_name = $wpdb->prefix . "hce_materials"; 

	$sql = "
	CREATE TABLE $table_name (
	  id bigint(20) unsigned NOT NULL auto_increment,
	  code varchar(20) NOT NULL default '',
	  unit varchar(10) NOT NULL default '',
	  basic_material varchar(200) NOT NULL default '',
	  basic_material_mass float(10,10) NOT NULL default 0,
	  component_1 varchar(200) NOT NULL default '',
	  component_1_mass float(10,10) NOT NULL default 0,
	  component_2 varchar(200) NOT NULL default '',
	  component_2_mass float(10,10) NOT NULL default 0,
	  component_3 varchar(200) NOT NULL default '',
	  component_3_mass float(10,10) NOT NULL default 0,
	  dap_factor float(10,10) NOT NULL default 0,
	  PRIMARY KEY  (id)
	) $charset_collate;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'hce_materials_version', $materials_ver );

} // end create materials table in DB

// update custom tables in DB
function hce_db_custom_tables_update() {
	global $wpdb;
	global $emissions_ver;
	global $materials_ver;

	$emissions_installed_ver = get_option( "hce_emissions_version" );
	$materials_installed_ver = get_option( "hce_materials_version" );

	if ( $emissions_installed_ver != $emissions_ver ) {
		hce_db_emissions_table();
	}
	if ( $materials_installed_ver != $materials_ver ) {
		hce_db_materials_table();
	}
} // update custom tables in DB

// populate emissions table
function hce_db_emissions_table_populate() {

	global $wpdb;

	$emissions_data_current_ver = get_option( "hce_emissions_data_version" );
	// data file
	$filename = HCE_BLOGTHEME. "/data/opendap.csv"; // relative path to data filename
	$line_length = "4096"; // max line lengh (increase in case you have longer lines than 1024 characters)
	$delimiter = ","; // field delimiter character
	$enclosure = '"'; // field enclosure character
	
	// open the data file
	$fp = fopen($filename,'r');

	if ( $fp !== FALSE ) { // if the file exists and is readable
	
		$table = $wpdb->prefix . "hce_emissions"; 
		$format = array(
			//'%d',
			'%s',
			'%s',
			'%s',
			'%s'
		);

		$line = 0;
		while ( ($fp_csv = fgetcsv($fp,$line_length,$delimiter,$enclosure)) !== FALSE ) { // begin main loop
			if ( $line == 0 ) { // check version
				$emissions_data_new_ver = $fp_csv[0];
				if ( $emissions_data_current_ver == $emissions_data_new_ver ) { return; /* stop: current version is up to date */ }

			} elseif ( $line == 1 ) { /* csv file headers */ }

			else {
				// preparing data to insert
				$opendap_code = $fp_csv[2];
				$emission_factor = round($fp_csv[3],5);
				$data = array(
					//'id' => is autoincrement
					'opendap_code' => $opendap_code,
					'type' => $fp_csv[0],
					'subtype' => $fp_csv[1],
					'emission_factor' => $emission_factor
				);
				$where = array(
					'opendap_code' => $opendap_code
				);
				// query to know if there is already rows for this opendap code
				$select_query = "SELECT opendap_code,emission_factor FROM $table WHERE opendap_code='$opendap_code' LIMIT 1";
				$select = $wpdb->get_results($select_query,OBJECT_K);
				if ( array_key_exists($opendap_code,$select) ) { // if there is a row for this code
					if ( $select[$opendap_code]->emission_factor != $emission_factor ) {
						/* update row */ $wpdb->update( $table, $data, $where, $format );
					}

				} else { // if there is no row for this code
					/* create row */ $wpdb->insert( $table, $data, $format );

				}

			} // end if not line 0
			$line++;

		} // end main loop
		fclose($fp);
		update_option( 'hce_emissions_data_version', $emissions_data_new_ver );

	} else { // if data file do not exist
		echo "<h2>Error</h2>
			<p>File with contents not found or not accesible.</p>
			<p>Check the path: " .$csv_filename. ". Maybe it has to be absolute...</p>";
	} // end if file exist and is readable

} // end populate emissions table
?>
