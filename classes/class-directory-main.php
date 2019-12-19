<?php
namespace BarrelDirectory\Classes;

use BarrelDirectory\Classes\Api\Rest_Api;
use BarrelDirectory\Classes\Db as DB;
use BarrelDirectory\Classes\Cpt\Cpt;
use BarrelDirectory\Classes\Acf\Acf_Setup;
use BarrelDirectory\Classes\Acf\Acf_Mapping;
use BarrelDirectory\Classes\Util\Page_Template;
use BarrelDirectory\Classes\Query\Geo_Query;
use BarrelDirectory\Classes\Admin\Instructor_Admin;
use BarrelDirectory\Classes\Admin\Instructor_Login;
use BarrelDirectory\Classes\Admin\Profile_Editor;
use BarrelDirectory\Classes\Admin\Password_Reset;
use BarrelDirectory\Classes\Admin\User_Registration;
use WP_Query;

Class Directory_Main {

  public function __construct(
    Instructor_Admin $instructor_admin,
    Instructor_Login $instructor_login,
    Profile_Editor $profile_editor,
    Acf_Setup $acf_setup,
    Rest_Api $rest_api,
    User_Registration $user_reg,
    Db\Db_Control $db_ctrl
  ) {
      $this->profile_editor = $profile_editor;
      $this->instructor_admin = $instructor_admin;
      $this->instructor_login = $instructor_login;
      $this->acf_setup = $acf_setup;
      $this->rest_api = $rest_api;
      $this->user_reg = $user_reg;
      $this->db_ctrl = $db_ctrl;
      /* // add this later on to keep code cleaner
      $this->rest_api = $api;
      $this->db = $db;
      $this->cpt = $cpt;
      $this->acf = $acf;
      $this->page_templates = $page_templates;
      $this->geo_query = $geo_query;
      $this->password_reset = $password_reset;
      */

      // exclude pages from cache
      $regex_path_patterns = array(
        '#^/directory/?#'
      );
      // Loop through the patterns.
      foreach ($regex_path_patterns as $regex_path_pattern) {
        if (preg_match($regex_path_pattern, $_SERVER['REQUEST_URI'])) {
          add_action( 'send_headers', array($this, 'add_header_nocache'), 15 );

          // No need to continue the loop once there's a match.
          break;
        }
      }
  }

  public function register_actions () {
    // Add an action to delete data from the custom table on post delete (not trashing)
    add_action( 'delete_post', array($this->db_ctrl, 'delete'), 10 );

    // Prefix the callback with the namespace or it won't be found
    add_action( 'plugins_loaded', array($this, 'init'), 1 );
    add_action( 'wp_enqueue_scripts', array($this, 'front_end_assets'));
    add_action( 'admin_enqueue_scripts', array($this, 'admin_assets' ));
  }

  // Starts the plugin by initializing classes, setting up hooks and more
  public function init() {
    // add profile image size
    add_image_size('profile-square', 620, 620, ['center','center']);

    // Instructor role and admin stuff
    $this->instructor_admin->register_actions();
    // $this->instructor_login->register_actions();
    $this->profile_editor->register_actions();

    // if a user is registering, use this process
    if ( !is_admin() && !empty($_POST['submit'] ) && !empty($_POST['email'] ) && !empty($_POST['password'] ) ) {
      $this->user_reg->init_user($_POST['fname'].' '.$_POST['lname'], $_POST['password'], $_POST['email'], $_POST['fname'], $_POST['lname']);
      $this->user_reg->registration_validation();
      if(!$this->user_reg->has_errors) $this->user_reg->complete_registration();
    }
    
    
    // add geo query vars/sql
    Geo_Query::get_instance();

    // add rest api routes
    $this->rest_api->register_actions();

    // add post types and taxonomies
    new Cpt();

    // if not editing on local, load acf groups from php
    // if($_ENV['PANTHEON_ENVIRONMENT'] !== 'lando'){
      $this->acf_setup->init_register();
    // }


    // map both new post types to the custom db
    new Acf_Mapping('studio');
    new Acf_Mapping('instructor');

    // add new page templates for display
    Page_Template::get_instance();

    new Password_Reset();
  }

  public function admin_assets () {
    $vars = [
      'tmpl_dir' => get_template_directory(),
      'style_dir' => get_stylesheet_directory_uri(),
      'mapbox' => [
        'key' => (get_field('mapbox_key', 'option')) ? get_field('mapbox_key', 'option') : 'pk.eyJ1Ijoicm9iZXJ0cmFlIiwiYSI6ImNqdndhOHFmejRhczYzeW9qYjJrMGQzY3QifQ.OZag22deq5xysi6cQuAEMw',
        'style' => (get_field('mapbox_style', 'option')) ? get_field('mapbox_style', 'option') : 'mapbox://styles/robertrae/cjvwoe8bp5bd91cqi6x6bkdjc',
        'latitude' => (get_field('default_latitude', 'option')) ? get_field('default_latitude', 'option') : '-74.2598655',
        'longitude' => (get_field('default_longitude', 'option')) ? get_field('default_longitude', 'option') : '40.6971494'
      ]
    ];
    // wp_die(print_r($vars));
    wp_enqueue_script('directory_admin_js',site_url().'/wp-content/plugins/barrel-directory/assets/admin.min.js', [], false, true);
    wp_localize_script( 'directory_admin_js', 'theme_vars', $vars);
  }

  public function front_end_assets() {
    $vars = [
      'tmpl_dir' => get_template_directory(),
      'style_dir' => get_stylesheet_directory_uri(),
      'mapbox' => [
        'key' => (get_field('mapbox_key', 'option')) ? get_field('mapbox_key', 'option') : 'pk.eyJ1Ijoicm9iZXJ0cmFlIiwiYSI6ImNqdndhOHFmejRhczYzeW9qYjJrMGQzY3QifQ.OZag22deq5xysi6cQuAEMw',
        'style' => (get_field('mapbox_style', 'option')) ? get_field('mapbox_style', 'option') : 'mapbox://styles/robertrae/cjvwoe8bp5bd91cqi6x6bkdjc',
        'latitude' => (get_field('default_latitude', 'option')) ? get_field('default_latitude', 'option') : '-74.2598655',
        'longitude' => (get_field('default_longitude', 'option')) ? get_field('default_longitude', 'option') : '40.6971494'
      ]
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
  }

  public function add_header_nocache() {
    header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
  }
}