<?php
/*
Template Name: Directory - Login Template
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

get_header();?>
<main id="main_content" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Log In',
      'title_class' => 'main-title',
      'add_link' => false
    ));?>
    <div class="container">
      <div class="login-register"><?php
        Lib::the_plugin_module('login-form', [
          'label_username' => 'E-mail Address',
          'label_password' => 'Passssword',
          'label_log_in' => 'Sign In',
          'redirect' => home_url().'/directory/profile-editor/',
          'form_id' => 'login__form'
        ]);?>
      </div>
    </div>
  </div>
</div>
  </section>
</main>

<?php get_footer();
