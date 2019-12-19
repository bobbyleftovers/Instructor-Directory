<?php
use BarrelDirectory\Classes\Admin\Password_Reset;
$errors = [];
$lost_password_sent = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'];?>

<div class="directory__password-reset-request admin-form" data-plugin-module="password-reset-request"><?php
  if ( is_user_logged_in()) {
    echo '<strong class="text-center w-100">You are already signed in. <a href="'.site_url().'/directory/my-account">Go to your profile</a></strong>';
  } else { 
    if ($lost_password_sent) {?>
      <div class="password-reset-request__success text-center">
        <a href="<?= home_url() ?>">Home Page</a><br>
        <a href="<?= home_url() ?>/directory">Directory</a><br>
        <a href="<?= home_url() ?>/directory/login">Login</a>
      </div><?php
    } else {?>
      <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post"><?php
        if ( isset( $_REQUEST['errors'] ) ) {
          $error_codes = explode( ',', $_REQUEST['errors'] );
      
          foreach ( $error_codes as $error_code ) {
              $errors[] = Password_Reset::get_error_message( $error_code );
          }
        }
        if ( count( $errors ) > 0 ) {
          foreach ( $errors as $error ) { ?>
            <div class="registration__error registration__error--server-side"><strong>ERROR: </strong><span class="error"><?= $error; ?></span></div><?php
          }
        }?>
      
        <div class="password-reset__input password-reset__input--user input-wrap">
          <label for="user_login"><?php _e( 'E-mail Address', 'personalize-login' ); ?></label>
          <input type="text" name="user_login" id="user_login" placeholder="Email Address" value="">
        </div>

        <div class="password-reset__input password-reset__input--submit input-wrap">
          <input aria-label="submit password reset request" type="submit" name="submit" class="button lostpassword-button" value="<?php _e( 'Reset Password', 'personalize-login' ); ?>"/>
        </div>

        <span class="admin-form__bottom-link"><a href="<?= site_url() ?>/directory/login" title="Go to Login">Cancel</a></span>
      </form><?php
    }
  }?>
</div>