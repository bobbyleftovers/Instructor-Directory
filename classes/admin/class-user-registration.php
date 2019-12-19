<?php
namespace BarrelDirectory\Classes\Admin;
use WP_Error;

Class User_Registration {
  
  public function __construct() {
    // sanitize user form input
    $this->username = null;
    $this->password = null;
    $this->email = null;
    $this->first_name = null;
    $this->last_name = null;
    $this->user_id = null;
  }

  public function init_user ($username, $password, $email, $fname, $lname) {
    // sanitize user form input
    $this->username = sanitize_user( $username );
    $this->password = esc_attr( $password );
    $this->email = sanitize_email( $email );
    $this->first_name = sanitize_text_field( $fname );
    $this->last_name = sanitize_text_field( $lname );
    $this->user_id = null;
  }

  public function registration_validation()  {
    $reg_errors = new WP_Error;

    if ( empty( $this->first_name ) ) {
      $reg_errors->add('field', 'First Name is required');
    }
    if ( empty( $this->last_name ) ) {
      $reg_errors->add('field', 'Last name is required');
    }
    if ( 4 > strlen( $this->username ) ) {
      $reg_errors->add( 'username_length', 'First name + last name must be at least 4 characters long' );
    }
    if ( username_exists( $this->username ) ) {
      $reg_errors->add('user_name', 'Sorry, that username already exists! Alter Your first or last name');
    }
    if ( ! validate_username( $this->username ) ) {
      $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
    }
    if ( empty( $this->email ) ) {
      $reg_errors->add('field', 'Email is required');
    }
    if ( email_exists( $this->email ) ) {
      $reg_errors->add( 'email', 'Email Already in use' );
    }
    if ( !is_email( $this->email ) ) {
      $reg_errors->add( 'email_invalid', 'Email is not valid' );
    }
    if ( empty( $this->password ) ) {
      $reg_errors->add('field', 'Password is required');
    }
    if ( 8 > strlen( $this->password ) ) {
      $reg_errors->add( 'password', 'Password length must be at least 8 characters' );
    }
    
    if ( is_wp_error( $reg_errors ) ) {
      echo '<div class="registration__errors">';
      foreach ( $reg_errors->get_error_messages() as $error ) {
        $this->has_errors = true;?>
        <div class="registration__error registration__error--server-side">
          <strong>ERROR</strong>: <?= $error ?><br/>
        </div><?php
      }
      echo '</div>';
    }
  }

  public function complete_registration() {
      $userdata = [
        'user_login'  => $this->username,
        'user_email'  => $this->email,
        'user_pass'   => $this->password,
        'first_name'  => $this->first_name,
        'last_name'   => $this->last_name,
        'nickname'    => $this->first_name . ' ' . $this->last_name,
        'role'        => 'instructor'
      ];
      $this->user_id = wp_insert_user( $userdata );

      // Insert instructor post
      $this->insertProfile();

      wp_clear_auth_cookie();
      wp_set_current_user( $this->user_id );
      wp_set_auth_cookie( $this->user_id );
      wp_redirect( site_url().'/directory/my-account' );
      exit;
  }

  function insertProfile() {
    $post_id = wp_insert_post([
      'post_author' => $this->user_id,
      'post_title'  => $this->username,
      'post_type'   => 'instructor'
    ]);

    update_field('contact_info_email', $this->email, $post_id);
    update_field('profile_post', $post_id, 'user_'. $this->user_id);
  }
}