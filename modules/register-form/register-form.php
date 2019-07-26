<div class="register-form" data-plugin-module="register-form">
  <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="directory__register admin-form">

    <div class="registration__input">
      <label for="firstname">First Name</label>
      <input type="text" name="fname" value="<?= ( isset( $_POST['fname']) ? $first_name : null ) ?>">
    </div>
    
    <div class="registration__input">
      <label for="website">Last Name</label>
      <input type="text" name="lname" value="<?= ( isset( $_POST['lname']) ? $last_name : null ) ?>">
    </div>
    
    <div class="registration__input">
      <label for="email">Email <strong>*</strong></label>
      <input type="text" name="email" value="<?= ( isset( $_POST['email']) ? $email : null ) ?>">
    </div>

    <div class="registration__input">
      <label for="password">Password <strong>*</strong></label>
      <input type="password" name="password" value="<?= ( isset( $_POST['password'] ) ? $password : null ) ?>">
    </div>

    <div class="registration__input">
      <label for="newsletter">Sign me up for the newsletter <strong>*</strong></label>
      <input type="checkbox" name="newsletter" value="<?= ( isset( $_POST['newsletter'] ) ? $newsletter : null ) ?>">
    </div>

    <br>
    <input class="button w-100" type="submit" name="submit" value="Register"/>
  </form>
  <p class="admin-form__bottom-link">Already have an account? <a href="<?=site_url().'directory/login'?>">Sign in here</a></p>
</div><?php

if ( isset($_POST['submit'] ) ) {
  registration_validation( $_POST['fname'].' '.$_POST['lname'], $_POST['password'], $_POST['email'], $_POST['fname'], $_POST['lname'] );
  
  // sanitize user form input
  // global $username, $password, $email, $first_name, $last_name;
  $username   =   sanitize_user( $_POST['fname'].' '.$_POST['lname'] );
  $password   =   esc_attr( $_POST['password'] );
  $email      =   sanitize_email( $_POST['email'] );
  $first_name =   sanitize_text_field( $_POST['fname'] );
  $last_name  =   sanitize_text_field( $_POST['lname'] );

  complete_registration( $username, $password, $email, $first_name, $last_name );
}

function registration_validation( $username, $password, $email, $first_name, $last_name )  {
  global $reg_errors;
  $reg_errors = new WP_Error;

  if ( empty( $username ) || empty( $password ) || empty( $email ) || empty( $first_name ) || empty( $last_name ) ) {
    $reg_errors->add('field', 'Required form field is missing');
  }
  if ( 4 > strlen( $username ) ) {
    $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
  }
  if ( username_exists( $username ) ) {
    $reg_errors->add('user_name', 'Sorry, that username already exists! Alter Your first or last name');
  }
  if ( ! validate_username( $username ) ) {
    $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
  }
  if ( 5 > strlen( $password ) ) {
    $reg_errors->add( 'password', 'Password length must be greater than 5' );
  }
  if ( !is_email( $email ) ) {
    $reg_errors->add( 'email_invalid', 'Email is not valid' );
  }
  if ( email_exists( $email ) ) {
    $reg_errors->add( 'email', 'Email Already in use' );
  }
  if ( is_wp_error( $reg_errors ) ) {
    foreach ( $reg_errors->get_error_messages() as $error ) {?>
      <div>
        <strong>ERROR</strong>: <?=$error ?><br/>
      </div><?php
    }
  }
}
function complete_registration($username, $password, $email, $first_name, $last_name) {
  // global $reg_errors, $username, $password, $email, $first_name, $last_name;
  if ( 1 > count( $reg_errors->get_error_messages() ) ) {
    $userdata = [
      'user_login'  => $username,
      'user_email'  => $email,
      'user_pass'   => $password,
      'first_name'  => $first_name,
      'last_name'   => $last_name,
      'nickname'    => $first_name . ' ' . $lastname,
      'role'        => 'instructor'
    ];
    $user_id = wp_insert_user( $userdata );

    // Insert instructor post
    insertProfile($username, $email, $user_id);
    // Set user ACF field to point at the new post
    echo '<p class="admin-form__bottom-link instructor__welcome-msg">Registration complete! <a href="'.site_url().'directory/login">Log in</a> to complete your profile!</p>';
  }
}

function insertProfile($username, $email, $user_id) {
  $post_id = wp_insert_post([
    'post_author' => $user_id,
    'post_title'  => $username,
    'post_type'   => 'instructor',
    // 'post_status' => 'publish'
  ]);

  update_field('contact_info_email', $email, $post_id);
  update_field('profile_post', $post_id, 'user_'. $user_id);
}
