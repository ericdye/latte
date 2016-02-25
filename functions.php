<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'latte' );
define( 'CHILD_THEME_URL', 'http://ericdye.it/latte' );
define( 'CHILD_THEME_VERSION', '1.0' );

//* Enqueue latte Scripts
add_action( 'wp_enqueue_scripts', 'latte_scripts' );
function latte_scripts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Yellowtail:400', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'dashicons' );

	//* latte
	wp_enqueue_script( 'latte-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );

}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );


// ************************************ FUNCTION SUGAR ************************************ //

//* Remove the header right widget area
unregister_sidebar( 'header-right' );

//* From Expose
//* Hook site avatar before site title
add_action( 'genesis_header', 'expose_site_gravatar', 5 );
function expose_site_gravatar() {

	$header_image = get_header_image() ? '<img alt="" src="' . get_header_image() . '" />' : get_avatar( get_option( 'admin_email' ), 224 );
	printf( '<div class="site-avatar"><a href="%s">%s</a></div>', home_url( '/' ), $header_image );

}

//* From http://my.studiopress.com/snippets/navigation-menus/
//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );


add_action( 'wp_enqueue_scripts', 'sk_enqueue_backstretch' );
/**
 * Set Featured Image as Header's background using Backstretch on singular entries.
 * For singular entries not having a Featured Image and other sections of the site,
 * like category archives, a default image will be set as Header's background.
 *
 * @author Sridhar Katakam
 * @link   http://sridharkatakam.com/set-featured-image-headers-background-using-backstretch-genesis/
 */
function sk_enqueue_backstretch() {

	wp_enqueue_script( 'backstretch', get_stylesheet_directory_uri() . '/js/min/jquery.backstretch.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'backstretch-set', get_stylesheet_directory_uri() . '/js/backstretch-set.js' , array( 'backstretch' ), '1.0.0', true );

	if ( has_post_thumbnail() && is_singular() ) {
		$featured_image_url = wp_get_attachment_url( get_post_thumbnail_id() );
		$backstretch_src = array( 'src' => $featured_image_url );
	} else {
		$default_header_url = get_stylesheet_directory_uri() . '/images/header-default.jpg';
		$backstretch_src = array( 'src' => $default_header_url );
	}

	wp_localize_script( 'backstretch-set', 'BackStretchImg', $backstretch_src );

}


//* From Beautiful Pro
//* Hook welcome message widget area before content
add_action( 'genesis_before_content', 'latte_welcome_message' );
function latte_welcome_message() {

	if ( ! is_front_page() || get_query_var( 'paged' ) >= 2 )
		return;

	genesis_widget_area( 'welcome-message', array(
		'before' => '<div class="welcome-message" class="widget-area">',
		'after'  => '</div>',
	) );

}

genesis_register_sidebar( array(
	'id'          => 'welcome-message',
	'name'        => __( 'Welcome Message', 'latte' ),
	'description' => __( 'This is the welcome message widget area.', 'latte' ),
) );


//* Modify breadcrumb arguments.
add_filter( 'genesis_breadcrumb_args', 'sp_breadcrumb_args' );
function sp_breadcrumb_args( $args ) {
	$args['home'] = 'Eric Dye';
	$args['sep'] = '&nbsp; // &nbsp;';
	$args['list_sep'] = ', '; // Genesis 1.5 and later
	$args['prefix'] = '<div class="breadcrumb">';
	$args['suffix'] = '</div>';
	$args['heirarchial_attachments'] = true; // Genesis 1.5 and later
	$args['heirarchial_categories'] = true; // Genesis 1.5 and later
	$args['display'] = true;
	$args['labels']['prefix'] = '';
	$args['labels']['author'] = 'Archives for ';
	$args['labels']['category'] = 'Archives for '; // Genesis 1.6 and later
	$args['labels']['tag'] = 'Archives for ';
	$args['labels']['date'] = 'Archives for ';
	$args['labels']['search'] = 'Search for ';
	$args['labels']['tax'] = 'Archives for ';
	$args['labels']['post_type'] = 'Archives for ';
	$args['labels']['404'] = 'Not found: '; // Genesis 1.5 and later
return $args;
}

//* Reposition the breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_before_content', 'genesis_do_breadcrumbs' );

//* Customize the post info function
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
if ( !is_page() ) {
	$post_info = '[post_date] [post_edit]';
	return $post_info;
}}

//* Customize the post meta function
add_filter( 'genesis_post_meta', 'sp_post_meta_filter' );
function sp_post_meta_filter($post_meta) {
if ( !is_page() ) {
	$post_meta = '[post_categories before=" " sep="&nbsp;//"]  [post_tags before=" " sep="&nbsp;//"]';
	return $post_meta;
}}

//* Modify the WordPress read more link
add_filter( 'the_content_more_link', 'sp_read_more_link' );
function sp_read_more_link() {
	return '<a class="more-link" href="' . get_permalink() . '">More, please...</a>';
}

//* Change the footer text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright] <a href="http://ericdye.it" title="Eric Dye">Eric Dye</a>  // <a href="http://ericdye.it/privacy-policy" title="Privacy Policy">Privacy Policy</a> // <a href="http://ericdye.it/comment-policy" title="Comment Policy">Comment Policy</a> // <a href="http://ericdye.it/archive" title="Archive">Archive</a>

	</br></br></br>

	<div id="site-creds"><div class="site-icons">
	<ul>

	<li class="wordpress">
	<a href="http://ericdye.it/lubs/wordpress" target="_blank" title="WordPress" alt="WordPress">WordPress</a>
	</li>

	<li class="wpengine">
	<a href="http://ericdye.it/lubs/wpengine" target="_blank" title="WP Engine" alt="WP Engine">WP Engine</a>
	</li>

	<li class="genesis">
	<a href="http://ericdye.it/lubs/genesis" target="_blank" title="Genesis Framework" alt="Genesis">Genesis</a>
	</li>

	</ul>
	</div></div>

	<div class="bottom-of-the-glass">
	<a href="http://ericdye.it/latte" title="latte" alt="latte">latte</a>
	</div>

	';

	return $creds;
}