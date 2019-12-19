<?php
namespace BarrelDirectory\Classes\Admin;

Class Instructor_Admin {

  public function  __construct() {}

  public function register_actions () {
    add_action( 'init', array($this, 'instructor_admin_redirect') );
    add_action( 'init', array($this, 'enable_role_capabilities') );
    add_filter( 'posts_where', array($this, 'hide_media_from_instructors') );
  }

  public function add_roles_on_plugin_activation() {
    add_role( 'instructor', 'Instructor', array( 'read' => true, 'edit_posts' => true, 'upload_files' => true ) );
  }
  
  // redirect all instructors outside the amin area
  public function instructor_admin_redirect(){
    if(!DEV_MODE){ // dev purposes
      if( is_admin() && !defined('DOING_AJAX') && current_user_can('instructor') ) {
        wp_redirect(home_url('/directory/my-account'));
        exit;
      }
    }
  }

  // allow instructors to have certain capabilities
  public function enable_role_capabilities( ) {
    if(!current_user_can('instructor')) return;
    $user_role = get_role('instructor');
    $user_role->add_cap('upload_files');
    $user_role->add_cap('edit_posts');
    $user_role->add_cap('edit_published_posts');
    $user_role->add_cap('read');
  }

  // Instructors should only be able to see the media files they have added
  public function hide_media_from_instructors( $where ){
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
}