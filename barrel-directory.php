<?php
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
namespace BarrelDirectory;

use BarrelDirectory\Classes\Directory_Main;
use BarrelDirectory\Classes\Admin;
use BarrelDirectory\Classes\Db as DB;
use BarrelDirectory\Classes\Acf\Acf_Setup;
use BarrelDirectory\Classes\Acf\Acf_Mapping;
use BarrelDirectory\Classes\Api\Rest_Api;
use BarrelDirectory\Classes\Admin\User_Registration;

// Include the autoloader so we can dynamically include classes.
require_once( 'autoload.php' );
// Define a constant for URIs
define( 'BARREL_DIRECTORY_PATH', __DIR__ );
define( 'BARREL_DIRECTORY_ASSETS_URI', site_url().'/wp-content/plugins/barrel-directory/assets/' );
define( 'ENTRY_TABLE_NAME', 'directory_entries' );
if(isset($_ENV['PANTHEON_ENVIRONMENT'])){
  define( 'DEV_MODE', $_ENV['PANTHEON_ENVIRONMENT'] === 'lando');
} else {
  define( 'DEV_MODE', true);
}

// Activation hooks: create DB tables, add user roles, etc
register_activation_hook( __FILE__, array('BarrelDirectory\Classes\Admin\Instructor_Admin', 'add_roles_on_plugin_activation') );
register_activation_hook(__FILE__, array('BarrelDirectory\Classes\Db\Db_Setup', 'on_activation') );
register_activation_hook( __FILE__, array('BarrelDirectory\Classes\Util\Page_Template', 'create_pages' ));

// deactivation hooks
register_deactivation_hook(__FILE__, array('BarrelDirectory\Classes\Db\Db_Setup', 'on_deactivation'));

// uninstall hooks: or drop DB tables
register_uninstall_hook(__FILE__, array('Db\Db_Setup', 'on_uninstall'));

$main = new Directory_Main(
  new Admin\Instructor_Admin,
  new Admin\Instructor_Login,
  new Admin\Profile_Editor,
  new Acf_Setup,
  new Rest_Api,
  new User_Registration,
  new Db\Db_Control
);
$login = new Admin\Instructor_Login();

$main->register_actions();
// $login->register_actions();
