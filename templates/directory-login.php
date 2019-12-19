<?php
/*
Template Name: Directory - Login Template
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// check that the user is logged in and is an instructor. if not, redirect them
$user = wp_get_current_user();
if (is_user_logged_in() && in_array( 'instructor', (array) $user->roles )) {
  // send user home
  header('Location: '.home_url().'/directory/my-account');
}

get_header();?>
<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Log In',
      'title_class' => 'h2 main-title--admin',
      'wrapper_classes' => 'directory__header--login'
    ));?>
    <div class="container">
      <div class="login-register"><?php
        Lib::the_plugin_module('login-form', [
          'label_username' => 'E-mail Address',
          'label_password' => 'Password',
          'label_log_in' => 'Sign In',
          'redirect' => home_url().'/directory/my-account',
          'form_id' => 'login__form'
        ]);?>
      </div>
    </div>
  </div>
</div>
  </section>
</main>

<?php get_footer();
