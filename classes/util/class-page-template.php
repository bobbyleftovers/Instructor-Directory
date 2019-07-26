<?php
namespace BarrelDirectory\Classes\Util;
use WP_Query;

class Page_Template {

  /**
   * A reference to an instance of this class.
   */
  private static $instance;

  /**
   * The array of templates that this plugin tracks.
   */
  protected $templates;

  /**
   * Returns an instance of this class. 
   */
  public static function get_instance() {

    if ( null == self::$instance ) {
      self::$instance = new Page_Template();
    } 

    return self::$instance;

  } 

  private function __construct() {

    $this->templates = array();

    // Add a filter to the attributes metabox to inject template into the cache.
    if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

      // 4.6 and older
      add_filter('page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ));

    } else {

      // Add a filter to the wp 4.7 version attributes metabox
      add_filter('theme_page_templates', array( $this, 'add_new_template' ));

    }

    // Add a filter to the save post to inject out template into the page cache
    add_filter('wp_insert_post_data', array( $this, 'register_project_templates' ) );

    // Add a filter to the template include to determine if the page has our 
    // template assigned and return it's path
    add_filter('template_include', array( $this, 'view_project_template') );

    add_filter('single_template', array($this, 'add_single_post_template'));

    // Add your templates to this array.
    $this->templates = array(
      'directory-front-end.php' => 'Directory - Front End',
      'directory-admin.php' => 'Directory - Account/Admin',
      'directory-login.php' => 'Directory - Login',
      'directory-register.php' => 'Directory - Register',
      'directory-password-reset.php' => 'Directory - Password Reset',
      'directory-account-main.php' => 'Directory - Account (main)',
      'directory-account-editor.php' => 'Directory - Account (profile editor)',
      'directory-account-location-editor.php' => 'Directory - Account (location editor)'
    );
  }
  /* Filter the single_template */
  function add_single_post_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'instructor' ) {
      if ( file_exists(  BARREL_DIRECTORY_PATH . '/templates/single-member.php' ) ) {
        return BARREL_DIRECTORY_PATH . '/templates/single-member.php';
      }
    }

    if ( $post->post_type == 'studio' ) {
      if ( file_exists(  BARREL_DIRECTORY_PATH . '/templates/single-studio.php' ) ) {
        return BARREL_DIRECTORY_PATH . '/templates/single-studio.php';
      }
    }

    return $single;

  }

  /**
   * Adds our template to the page dropdown for v4.7+
   *
   */
  public function add_new_template( $posts_templates ) {
    $posts_templates = array_merge( $posts_templates, $this->templates );
    // $this->create_pages();
    return $posts_templates;
  }

  /**
   * Adds our template to the pages cache in order to trick WordPress
   * into thinking the template file exists where it doens't really exist.
   */
  public function register_project_templates( $atts ) {

    // Create the key used for the themes cache
    $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

    // Retrieve the cache list. 
    // If it doesn't exist, or it's empty prepare an array
    $templates = wp_get_theme()->get_page_templates();
    if ( empty( $templates ) ) {
      $templates = array();
    } 

    // New cache, therefore remove the old one
    wp_cache_delete( $cache_key , 'themes');

    // Now add our template to the list of templates by merging our templates
    // with the existing templates array from the cache.
    $templates = array_merge( $templates, $this->templates );

    // Add the modified cache to allow WordPress to pick it up for listing
    // available templates
    wp_cache_add( $cache_key, $templates, 'themes', 1800 );

    return $atts;

  } 

  /**
   * Checks if the template is assigned to the page
   */
  public function view_project_template( $template ) {
    
    // Get global post
    global $post;

    // Return template if post is empty
    if ( ! $post ) {
      return $template;
    }

    // Return default template if we don't have a custom one defined
    if ( ! isset( $this->templates[get_post_meta( 
      $post->ID, '_wp_page_template', true 
    )] ) ) {
      return $template;
    } 

    $file = BARREL_DIRECTORY_PATH . '/templates/' . get_post_meta( 
      $post->ID, '_wp_page_template', true
    );

    // Just to be safe, we check if the file exist first
    if ( file_exists( $file ) ) {
      return $file;
    } else {
      echo $file;
    }

    // Return template
    return $template;

  }

  /****
   * WIP: Create the pages we need and set their templates
   */
  static function create_pages() {
    // Use this ID to represent the author of the pages

    $admin_id = self::get_admin_ID();

    // Top level page
    $directory = array(
      'post_title'    => 'Directory',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_author'   => 1,
      'page_template' => 'directory-front-end-template.php'
    );
    $query = new WP_Query( array('name' => 'directory' ));
    if ( ! $query->have_posts() ) {
      $directory_id = wp_insert_post( $directory, $error_obj );
    } else {
      $dir_post = get_posts([
        'name' => 'directory',
        'post_type' =>'page',
        'post_status' => 'publish',
        'numberposts' => 1
      ]);
      $directory_id = $dir_post[0]->ID;
    }

    // Subpages of /directory
    $login = array(
      'post_title'    => 'Login',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $directory_id,
      'post_author'   => 1,
      'page_template' => 'directory-login-template.php'
    );
    $query = new WP_Query( array('name' => 'login' ));
    if ( ! $query->have_posts() ) {
      $login_id = wp_insert_post( $login, $error_obj );
    }

    $register = array(
      'post_title'    => 'Register',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $directory_id,
      'post_author'   => 1,
      'page_template' => 'directory-admin-template.php'
    );
    $query = new WP_Query( array('name' => 'register', 'post_type' => 'page') );
    if ( ! $query->have_posts() ) {
      $register_id = wp_insert_post( $register, $error_obj );
    }

    $pw_reset = array(
      'post_title'    => 'Password Reset',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $directory_id,
      'post_author'   => 1,
      'page_template' => 'directory-admin-template.php'
    );
    $query = new WP_Query( array('name' => 'password-reset' ));
    if ( ! $query->have_posts() ) {
      $pw_reset_id = wp_insert_post( $pw_reset, $error_obj );
    }

    $account_main = array(
      'post_title'    => 'My Account',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $directory_id,
      'post_author'   => 1,
      'page_template' => 'directory-admin-template.php'
    );
    $query = new WP_Query( array('name' => 'my-account' ));
    if ( ! $query->have_posts() ) {
      $account_main_id = wp_insert_post( $account_main, $error_obj );
    } else {
      $acct_post = get_posts([
        'name' => 'directory',
        'post_type' =>'page',
        'post_status' => 'publish',
        'numberposts' => 1
      ]);
      $account_main_id = $acct_post[0]->ID;
    }

    // Subpages of /my-account
    $profile_editor = array(
      'post_title'    => 'Profile Editor',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $account_main_id,
      'post_author'   => 1,
      'page_template' => 'directory-admin-template.php'
    );
    $query = new WP_Query( array('name' => 'profile-editor' ));
    if ( ! $query->have_posts() ) {
      $profile_editor_id = wp_insert_post( $profile_editor, $error_obj );
    }

    $location_editor = array(
      'post_title'    => 'Location Editor',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_parent'   => $account_main_id,
      'post_author'   => 1,
      'page_template' => 'directory-admin-template.php'
    );
    $query = new WP_Query( array('name' => 'location-editor' ));
    if ( ! $query->have_posts() ) {
      $location_editor_id = wp_insert_post( $location_editor, $error_obj );
    }

  }

  /*****
   * Find and return an administrative ID
   */
  public function get_admin_ID() {
    //Grab wp DB
    global $wpdb;
    //Get all users in the DB
    $wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");

    //Loop through all users
    foreach ( $wp_user_search as $user_id ) {
      $id = $user_id->ID;
      
      //Grab the user info of current ID
      $current_user = get_userdata($id);
      
      //Current user level
      $user_level = $current_user->user_level;
      
      // Check if this is an admin. levels 8, 9 and 10 are admin
      if($user_level >= 8){
        return $id;
      }
    }
    return false;
    
  }
}