<?php
/*
Template Name: Directory - Login Template
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// Before we do anything, make sure the user is logged in and has the right role
// $user = wp_get_current_user();
// if ( in_array( 'instructor', (array) $user->roles ) ) {
//   // send instructor to profile editor page
//   header('Location: '.home_url().'/directory/profile-editor/');
// } else if($user->ID !== 0) {
//   // send user home
//   header('Location: '.home_url());
// }

get_header();?>
<main id="main_content" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => get_the_title(),
      'title_class' => 'main-title',
      'add_link' => false
    ));?>
    <div class="container">
      <div class="login-register">
        <div class="directory__login form">
          <h2 class="text-center">Log In</h2><?php
          wp_login_form( array(
            'redirect' => home_url().'/directory/profile-editor/',
            'form_id' => 'login__form'
          ));?>
          <a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="Lost Password">Lost Password?</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer();
