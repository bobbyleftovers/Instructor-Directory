<?php
namespace BarrelDirectory\Classes\Admin;

Class Password_Reset {
  public function __construct () {
    add_action( 'login_form_lostpassword', array($this, 'redirect_password_lost') );
    add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );
    add_filter( 'retrieve_password_message', array( $this, 'replace_retrieve_password_message' ), 10, 4 );
    add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
    add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
    add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );
    add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );
  }

  public function redirect_password_lost() {
    
    // Use normal redirect for non-instructors
    if(is_admin()) return;
    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
      if ( is_user_logged_in() ) {
        // $this->redirect_logged_in_user();
        exit;
      }

      wp_redirect( home_url( 'directory/password-reset-request' ) );
      exit;
    }
  }

  public function do_password_lost() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
      $errors = retrieve_password();
      // die(print_r($errors));
      if ( is_wp_error( $errors ) ) {
        // Errors found
        $redirect_url = home_url( 'directory/password-reset-request' );
        $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
      } else {
        // Email sent
        $redirect_url = home_url( 'directory/password-reset-request' );
        $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
      }

      wp_redirect( $redirect_url );
      exit;
    }
  }

  public function do_password_reset() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
      $rp_key = $_REQUEST['rp_key'];
      $rp_login = $_REQUEST['rp_login'];

      $user = check_password_reset_key( $rp_key, $rp_login );
      if ( ! $user || is_wp_error( $user ) ) {
        if ( $user && $user->get_error_code() === 'expired_key' ) {
          wp_redirect( home_url( 'directory/login?login=expiredkey' ) );
        } else {
          wp_redirect( home_url( 'directory/login?login=invalidkey' ) );
        }
        exit;
      }
      if ( isset( $_POST['pass1'] ) ) {
        if ( $_POST['pass1'] != $_POST['pass2'] ) {
          // Passwords don't match
          $redirect_url = home_url( 'directory/password-reset-form' );
 
          $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
          $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
          $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

          wp_redirect( $redirect_url );
          exit;
        }
        if ( empty( $_POST['pass1'] ) ) {
          // Password is empty
          $redirect_url = home_url( 'directory/password-reset-form' );
  
          $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
          $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
          $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

          wp_redirect( $redirect_url );
          exit;
        }
        // Parameter checks OK, reset password
        reset_password( $user, $_POST['pass1'] );
        wp_redirect( home_url( 'directory/login?password=changed' ) );
      } else {
        echo 'Invalid request.';
      }
      exit;
    }
  }

  public function get_error_message( $error_code ) {
    $error_msg = '';
    switch ( $error_code ) {
      case 'empty_username':
        $error_msg = 'You do have an email address, right?';
        break;

      case 'empty_password':
        $error_msg = 'You need to enter a password to login.';
        break;

      case 'invalid_username':
        $error_msg = 'We don\'t have any users with that email address. Maybe you used a different one when signing up?';
        break;

      case 'incorrect_password':
        $error_msg = 'The password you entered wasn\'t quite right. <a href="'.site_url().'/directory/password-reset-request">Did you forget your password</a>?';
        break;

      case 'empty_username':
        $error_msg = 'You need to enter your email address to continue.';
        break;
     
      case 'invalid_email':
        $error_msg = 'There are no users registered with this email address.';
        break;

      case 'invalidcombo':
        $error_msg = 'There are no users registered with this email address.';
        break;
      
      case 'password_reset_mismatch':
        $error_msg = 'Password fields must match.';
        break;
      
      case 'password_reset_empty':
        $error_msg = 'You must provide a password in both fields.';
        break;

      default:
        $error_msg = 'An unknown error occurred. Please try again later.';
        break;
    }
     
    return $error_msg;
  }

  public function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    // Create new message
    $msg  = 'Hello!'. "\r\n\r\n";
    $msg .= sprintf( 'You asked us to reset your password for your account using the email address %s.', $user_login ) . "\r\n\r\n";
    $msg .= "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen." . "\r\n\r\n";
    $msg .= 'To reset your password, visit the following address:' . "\r\n\r\n";
    $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
    $msg .= 'Thanks!' . "\r\n";
 
    return $msg;
  }

  public function redirect_to_custom_password_reset() {
    if('GET' === $_SERVER['REQUEST_METHOD']) {
      // Verify key / login combo
      // die(print_r($_REQUEST));
      $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
      // die($user);
      if ( ! $user || is_wp_error( $user ) ) {
        if ( $user && $user->get_error_code() === 'expired_key' ) {
          wp_redirect( home_url( 'directory/password-reset-form?login=expiredkey' ) );
        } else {
          wp_redirect( home_url( 'directory/password-reset-form?login=invalidkey' ) );
        }
        exit;
      }

      $redirect_url = home_url( 'directory/password-reset-form' );
      $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
      $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

      wp_redirect( $redirect_url );
      exit;
    }
  }
}