<?php
/*
Template Name: Directory - Login Template
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// Before we do anything, make sure the instructor isn't re-registering
$user = wp_get_current_user();
if ( in_array( 'instructor', (array) $user->roles ) ) {
  // send instructor to profile editor page
  header('Location: '.home_url().'/directory/my-account');
}

get_header();?>
<main id="main_content" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Create An Account',
      'title_class' => 'main-title',
      'add_link' => false
    ));?>
    <div class="container">
      <div class="login-register">
        <div class="directory__registration form"><?php
        Lib::the_plugin_module('register-form');?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer();