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

?>
