<?php
/*
Template Name: Directory - User Admin Main Area (logged in)
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// check that the user is logged in and is an instructor. if not, redirect them
$user = wp_get_current_user();
if (!is_user_logged_in() || !in_array( 'instructor', (array) $user->roles )) {
  // send user home
  header('Location: '.home_url().'/directory/login');
}

get_header();

$user_id = get_current_user_id();

if( get_field('profile_post', 'user_'.$user_id) ){
  $profile_id = get_field('profile_post', 'user_'.$user_id);
}
$profile_post = get_post($profile_id);?>
<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container account-admin"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Your Account',
      'title_class' => 'h2 main-title--admin',
      'admin' => true,
      'wrapper_classes' => 'directory__header--admin directory__header--title-link single-link',
      'right_link_url' => wp_logout_url(home_url().'/directory/login'),
      'right_link_copy' => 'Log Out',
      'is_published' => ($profile_post->post_status === 'publish') ? true : false,
    ));?>
    <div class="container"><?php
      Lib::the_plugin_module('profile-viewer', array(
        'profile_id' => $profile_id,
        'user_id' => $user_id,
        'is_published' => ($profile_post->post_status === 'publish') ? true : false
      ));?>
    </div>
</section>
</main><?php

get_footer();?>
