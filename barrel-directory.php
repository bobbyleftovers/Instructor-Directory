<?php
namespace BarrelDirectory;

use BarrelDirectory\Classes\Api\Rest_Api;
use BarrelDirectory\Classes\Db as DB;
use BarrelDirectory\Classes\Cpt\Cpt;
use BarrelDirectory\Classes\Acf;
use BarrelDirectory\Classes\Util\Page_Template;
use BarrelDirectory\Classes\Query\Geo_Query;
use WP_Query;

/*
 *
 * This file is read by WordPress to generate the plugin information in the
 * plugin admin area. This file also Classes all of the dependencies used by
 * the plugin, registers the activation and deactivation functions, and defines
 * a function that starts the plugin.
 *
 * @since             0.1.0
 * @package           BarrelDirectory
 *
 * @wordpress-plugin
 * Plugin Name: Barrel Directory
 * Plugin URI: 
 * Description: A plugin that manages a directory of organization instructors and allows for location, position and other meta data per person/location.
 * Version: 0.1
 * Author: BarrelNY
 * Author URI: https://www.barrelny.com/
 * Text Domain: 
 * Domain Path: 
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


// Define a constant for URIs
define( 'BARREL_DIRECTORY_PATH', __DIR__ );
define( 'BARREL_DIRECTORY_ASSETS_URI', site_url().'/wp-content/plugins/barrel-directory/assets/' );
define( 'ENTRY_TABLE_NAME', 'directory_entries' );

// Activation/Deactivation/Delete hooks create or drop DB tables, add user roles, etc
function add_roles_on_plugin_activation() {
  add_role( 'instructor', 'Instructor', array( 'read' => true, 'edit_posts' => true, 'upload_files' => true ) );
}
register_activation_hook( __FILE__, 'BarrelDirectory\add_roles_on_plugin_activation' );
register_activation_hook(__FILE__, array('BarrelDirectory\Classes\Db\Db_Setup', 'on_activation'));
register_activation_hook( __FILE__, array('BarrelDirectory\Classes\Util\Page_Template', 'create_pages' ));
register_deactivation_hook(__FILE__, array('BarrelDirectory\Classes\Db\Db_Setup', 'on_deactivation'));
register_uninstall_hook(__FILE__, array('BarrelDirectory\Classes\Db\Db_Setup', 'on_uninstall'));


// allow instructors to have certain capabilities
function enable_role_capabilities( ) {
  $role = 'instructor';
  if(!current_user_can($role)) return;
  $user_role = get_role( $role );
  $user_role->add_cap('upload_files');
  $user_role->add_cap('edit_posts');
  $user_role->add_cap('edit_published_posts');
  $user_role->add_cap('read');
}

add_action('init', 'BarrelDirectory\enable_role_capabilities');

// Instructors should only be able to see the media files they have added
function hide_media_from_instructors( $where ){
    global $current_user;

    if( is_user_logged_in() ){
      if(!current_user_can('instructor')) return $where; // bail if not an instructor
         // logged in user, but are we viewing the library?
         if( isset( $_POST['action'] ) && ( $_POST['action'] == 'query-attachments' ) ){
            // here you can add some extra logic if you'd want to.
            $where .= ' AND post_author='.$current_user->data->ID;
        }
    }

    return $where;
}

add_filter( 'posts_where', 'BarrelDirectory\hide_media_from_instructors' );

// Include the autoloader so we can dynamically include classes.
require_once( 'autoload.php' );

// Prefix the callback with the namespace or it won't be found
add_action( 'plugins_loaded', 'BarrelDirectory\barrel_directory_init' );

// Starts the plugin by initializing classes, setting up hooks and more
function barrel_directory_init() {
  
  // add profile image size
  add_image_size('profile-square', 620, 620, ['center','center']);
  
  // add geo query vars/sql
  Geo_Query::get_instance();

  // add rest api routes
  new Rest_Api();

  // add post types and taxonomies
  new Cpt();

  // if not editing on local, load acf groups from php
  if($_ENV['PANTHEON_ENVIRONMENT'] !== 'lando'){
    new Acf\Acf_Setup();
  }

  // map both new post types to the custom db
  new Acf\Acf_Mapping('studio');
  new Acf\Acf_Mapping('instructor');

  // add new page templates for display
  Page_Template::get_instance();
}

// redirect all instructors outside the amin area
function instructor_admin_redirect(){
  // if($_ENV['PANTHEON_ENVIRONMENT'] !== 'lando'){ // dev purposes
  if( is_admin() && !defined('DOING_AJAX') && current_user_can('instructor') ) {
    wp_redirect(home_url('/directory/profile-editor' ));
    exit;
  }
  // }
}
add_action('init','BarrelDirectory\instructor_admin_redirect');
add_action('wp_enqueue_scripts', function(){
  $vars = [
    'tmpl_dir' => get_template_directory(),
    'style_dir' => get_stylesheet_directory_uri()
  ];

  // Compiled css/js
  wp_enqueue_style('directory_css',site_url().'/wp-content/plugins/barrel-directory/assets/main.min.css');
  wp_enqueue_script('directory_js',site_url().'/wp-content/plugins/barrel-directory/assets/main.min.js', [], false, true);
  wp_localize_script( 'directory_js', 'theme_vars', $vars);
  
  
  /****** TODO: GET THE BELOW ASSETS FROM NPM INSTEAD -vvv- ******/
  // Mapbox CSS
  wp_enqueue_style('directory_mapbox_css', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css');
  // Mapbox GL JS
  wp_enqueue_script('directory_mapbox_js', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js');
});

// Add an action to delete data from the custom table on post delete (not trashing)
add_action( 'delete_post', array('BarrelDirectory\Classes\Db\Db_Control', 'delete'), 10 );
