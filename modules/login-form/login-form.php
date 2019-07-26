<?php
$params = [];
($label_username) ?  $params['label_username'] = $label_username : '';
($label_password) ? $params['label_password'] = $label_password : '';
($label_log_in) ? $params['label_log_in'] = $label_log_in : '';
($redirect) ? $params['redirect'] = $redirect : '';
($form_id) ? $params['form_id'] = $form_id : '';?>

<div class="directory__login admin-form" data-plugin-module="login-form"><?php
  wp_login_form($params);?>
  <span class="admin-form__bottom-link"><a href="<?= wp_lostpassword_url( get_permalink() ); ?>" title="Lost Password">Forgot your password?</a></span>
  <span class="admin-form__bottom-link">Don't have an account? <a href="<?= site_url().'/directory/register' ?>" title="Lost Password">Register Now</a></span>
  <span class="admin-form__bottom-link admin-form__bottom-link--home"><a href="<?= site_url()?>" title="Lost Password">Return to Home</a></span>
</div>