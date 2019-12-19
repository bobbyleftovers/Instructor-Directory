<?php
use BarrelDirectory\Classes\Admin\Password_Reset;
$errors = [];?>
<div class="directory__password-reset-form admin-form" data-plugin-module="password-reset-form">
  <?php
    if ( is_user_logged_in() && !DEV_MODE) {
      echo '<strong class="text-center w-100">You are already signed in. <a href="'.site_url().'/directory/my-account">Go to your profile</a></strong>';
    } else {
      if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
        $login = $_REQUEST['login'];
        $key = $_REQUEST['key'];

        // Error messages
        if ( isset( $_REQUEST['error'] ) ) {
          $error_codes = explode( ',', $_REQUEST['error'] );
          foreach ( $error_codes as $code ) {
            $errors[] = Password_Reset::get_error_message( $code );
          }
        }
        $errors;?>
      
        <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
          <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $login ); ?>" autocomplete="off" />
          <input type="hidden" name="rp_key" value="<?php echo esc_attr( $key ); ?>" /><?php

          if ( count( $errors ) > 0 ){
            foreach ( $errors as $error ){ ?>
              <p><?= $error; ?></p><?php
            }
          }?>

          <div class="password-reset__input password-reset__input--pass1 input-wrap">
              <label for="pass1">New password</label>
              <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
          </div>
          <div class="password-reset__input password-reset__input--pass2 input-wrap">
              <label for="pass2">Repeat new password</label>
              <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
          </div>
            
          <p class="description"><?php echo wp_get_password_hint(); ?></p>
            
          <div class="password-reset__input password-reset__input--submit input-wrap resetpass-submit">
              <input type="submit" name="submit" id="resetpass-button" class="button" value="Reset Password" />
          </div>
        </form><?php
      } else { ?>
        <p>Sorry, that password reset link is invalid. If you need to reset your password you can do so <a href="<?=site_url()?>/directory/password-reset-request">here</a> or you can log in <a href="<?=site_url()?>/directory/login">here</a>.</p><?php
      }
    }?>
  </div>