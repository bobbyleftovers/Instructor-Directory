<?php
use BarrelDirectory\Classes\Admin\Instructor_Login;

$params = [
  'id_username' => 'login__user',
	'id_password' => 'login__pass',
];
($label_username) ?  $params['label_username'] = $label_username : '';
($label_password) ? $params['label_password'] = $label_password : '';
($label_log_in) ? $params['label_log_in'] = $label_log_in : '';
($redirect) ? $params['redirect'] = $redirect : '';
($form_id) ? $params['form_id'] = $form_id : '';

// Check if user just updated password
$password_updated = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';?>

<div class="directory__login admin-form" data-plugin-module="login-form"><?php
  if($password_updated){
    echo '<strong class="pw-reset__message">Your Password has been updated</strong>';
  }
  if(isset($_GET['login']) && $_GET['login'] === 'failed') {
    echo '<strong class="pw-reset__message">Username and/or password were invalid</strong>';
  }

  wp_login_form($params);?>
  <span class="admin-form__bottom-link"><a href="<?= wp_lostpassword_url( get_permalink() ); ?>" title="Lost Password">Forgot your password?</a></span>
  <span class="admin-form__bottom-link">Don't have an account? <a href="<?= site_url().'/directory/register' ?>" title="Register Now">Register Now</a></span>
  <span class="admin-form__bottom-link admin-form__bottom-link--home"><a href="<?= site_url()?>" title="Go to site home">Return to Home</a></span>
</div>