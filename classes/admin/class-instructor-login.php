<?php
namespace BarrelDirectory\Classes\Admin;

Class Instructor_Login {

  public function  __construct() {}

  public function register_actions () {
    add_filter( 'authenticate', array($this, 'authenticate'), 10, 3);
    // add_filter( 'login_redirect', array($this, 'login_redirect'), 10, 3);
    // add_filter( 'wp_login_errors', array($this, 'login_errors'), 10, 2);
    // add_filter( 'shake_error_codes', array($this, 'shake_error_codes'), 10, 1);
    add_action( 'wp_login_failed', array($this, 'login_failed'), 10, 1);
    // add_action( 'login_form', function(){
    //   echo 'login form<br>';
    //   // print_r()
    // });
    // add_action( 'login_init', function(){
    //   echo 'login_init<br>';
    //   // print_r()
    // });
  }

  public function login_init () {}

  public function login_form () {}

  public function authenticate ($user, $username, $password) {
    // this still needs to account for incorrect user/pass when both are filled out
    if ( empty( $username ) || empty( $password ) ) {
      do_action( 'wp_login_failed', $user );
    }
    return $user;
  }

  // redirect all instructors outside the amin area
  public function login_redirect ( $redirect_to = false, $requested_redirect_to = false, $user = false ) {
    // $redirect_to = site_url('directory/login');
  }

  public function login_failed ($username) {
    
    $referrer = $_SERVER['HTTP_REFERER'];
   
    // if there's a valid referrer, and it's not the default log-in screen
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( site_url() . '/directory/login?login=failed' );
      exit;
   }
  }

  // use this to add/remove errors that noramlly cause the wp-login to shake if present
  public function shake_error_codes ($errors) {
    return $errors;
  }

  public function login_errors ($error_obj, $redirect_to) {
    $redirect_to = site_url('directory/login');
    if($error_obj) {
      foreach ($error_obj->errors as $key => $value) {}
      
      return $error_obj;
    }
    wp_redirect($redirect_to);
  }

  public function get_error_message( $error_code ) {
    // $error_msg = '';
    // switch ( $error_code ) {
    //   case 'empty_username':
    //     $error_msg = 'You do have an email address, right?';
    //     break;
    // }
  }
}